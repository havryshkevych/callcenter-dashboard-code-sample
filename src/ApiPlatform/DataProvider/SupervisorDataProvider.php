<?php

namespace App\ApiPlatform\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Doctrine\RowNumberFunction;
use App\Entity\User;
use App\Enum\User\Role;
use App\Object\User\SupervisorOutput;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class SupervisorDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var QueryCollectionExtensionInterface[] */
    private iterable $collectionExtensions;

    public function __construct(
        private RequestStack $requestStack,
        private ManagerRegistry $managerRegistry,
        iterable $collectionExtensions = []
    ) {
        $this->collectionExtensions = $collectionExtensions;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $manager->getConfiguration()->addCustomStringFunction('ROW_NUMBER', RowNumberFunction::class);
        /** @var UserRepository $repository */
        $repository = $manager->getRepository($resourceClass);

        $request = $this->requestStack->getCurrentRequest();
        $dialogs_date = (array)$request->query?->get("dialogs_date") ?: ($context['dialogs_date'] ?? []);
        $scorings = $repository->buildSupervisorsRanking($dialogs_date);
        $activeTime = $repository->activeTime($dialogs_date);

        $supervisors = [];
        foreach ($scorings as $k => $scoring) {
            $supervisors[$scoring['supervisor']][$k] = $scoring;
        }
        foreach ($supervisors as $supervisor_id => $supervisorScorings) {
            $supervisorOutput = new SupervisorOutput();
            $supervisor = $repository->find($supervisor_id);
            $supervisorOutput->setName($supervisor->getName())
                ->setId($supervisor->getId());
            $supervisor_at = array_filter($activeTime, fn($at) => $at['user_id'] === $supervisor_id);
            if (!empty($supervisor_id)) {
                $supervisorOutput->setActiveTime(round(array_sum(array_column($supervisor_at, 'total_seconds')) / 60 / 60, 2) );
            }
            $scoringsCriticalCount = count(array_filter($supervisorScorings, fn ($s) => $s['critical_errors'] != null));
            $supervisorOutput->setScoringRatio(count($supervisorScorings) ? round(((count($supervisorScorings) - $scoringsCriticalCount) / count($supervisorScorings)) * 100, 2) : 0);
            $supervisorOutput->setZone($supervisor->getCurrentRank(Role::SUPERVISOR(), $dialogs_date)?->getZone());
            $supervisors[$supervisor_id] = $supervisorOutput;
        }

        usort($supervisors, function(SupervisorOutput $a, SupervisorOutput $b) {
            if ($a->getScoringRatio() === $b->getScoringRatio()) {
                return $b->getActiveTime() - $a->getActiveTime();
            }

            return $b->getScoringRatio() <=> $a->getScoringRatio();
        });

        return $supervisors;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass
            && ($operationName == 'scoringSupervisor' && @$context['output']['class'] === SupervisorOutput::class);
    }
}