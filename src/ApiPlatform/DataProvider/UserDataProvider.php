<?php

namespace App\ApiPlatform\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\FilterEagerLoadingExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Doctrine\RowNumberFunction;
use App\Entity\User;
use App\Enum\Dialog\Type;
use App\Enum\User\Role;
use App\Object\User\UserOutput;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

final class UserDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
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

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass
            && ($operationName == 'scoring' && @$context['output']['class'] === UserOutput::class);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $manager->getConfiguration()->addCustomStringFunction('ROW_NUMBER', RowNumberFunction::class);

        /** @var UserRepository $repository */
        $repository = $manager->getRepository($resourceClass);

        $queryBuilder = $repository->createQueryBuilder('u');

        $request = $this->requestStack->getCurrentRequest();
        $dialogs_date = (array)$request->query?->get("dialogs_date") ?: ($context['dialogs_date'] ?? []);
        $dialogs = $repository->dialogsScoring($dialogs_date);
        $activeTime = $repository->activeTime($dialogs_date);
        $knowledgeScoring = $repository->knowledge($dialogs_date);
        $repository->buildRanking($queryBuilder, $dialogs_date);
        $results = $queryBuilder->getQuery()->getResult();

        $users = array_map(function ($user) use ($results, $dialogs, $activeTime, $knowledgeScoring, $dialogs_date) {
            return $this->transform($user, count($results), $dialogs, $activeTime, $knowledgeScoring, $dialogs_date);
        }, $results);
        $users = array_filter($users, function(UserOutput $user) {
            return $user->getActiveTime() || $user->getTotalScore();
        });
        usort($users, function(UserOutput $a, UserOutput $b) {
            if ($a->getTotalScore() === $b->getTotalScore()) {
                return $b->getActiveTime() - $a->getActiveTime();
            }

            return $b->getTotalScore() <=> $a->getTotalScore();
        });
        return $users;
    }

    private function calcTotalScore(UserOutput $user): UserOutput
    {
        $totalScore = (
            $user->getActiveTime() * 0 + //0.2
            $user->getServiceLevel() * 0 + //0.2
            $user->getServiceLevelAverageSpeedAnswer() * 0 + //0.15
            $user->getKnowledge() * 0.3 +
            $user->getScoring() * 0.7
            );

        return $user->setTotalScore(round($totalScore, 2));
    }

    private function transform($data, $total, $dialogs, $activeTimes, $knowledgeScoring, $dialogs_date): UserOutput
    {
        /** @var User $user */
        $user = $data[0];
        $userDialogs = array_filter($dialogs, fn ($dialog) => $dialog['user_id'] === $user->getId());
        $userCallDialogs = array_filter($userDialogs, fn ($dialog) => $dialog['type'] === Type::CALL());
        $userChatDialogs = array_filter($userDialogs, fn ($dialog) => $dialog['type'] === Type::CHAT());
        $callPoints = empty($userCallDialogs) ? 0 : array_sum(array_column($userCallDialogs, 'points')) / count($userCallDialogs);
        $chatPoints = empty($userChatDialogs) ? 0 : array_sum(array_column($userChatDialogs, 'points')) / count($userChatDialogs);

        $userActiveTimes = array_filter($activeTimes, fn ($at) => $at['user_id'] === $user->getId());
        $activeTimeTotal = array_sum(array_column($userActiveTimes, 'total_seconds')) / 60 / 60;

        $userKnowledgeScoring = array_filter($knowledgeScoring, fn ($at) => $at['user_id'] === $user->getId());
        $knowledgeScoringTotal = array_sum(array_column($userKnowledgeScoring, 'total_score'));

        $criticalErrors = array_sum(array_column($userDialogs, 'critical_errors')); // количество критических ошибок в оцениваниях

        $scoring = count($userDialogs) ? 100 - ((count($userDialogs) - (count($userDialogs) -
                    count(array_filter($userDialogs, fn ($dialog) => $dialog['critical_errors'] > 0)))) / count($userDialogs) * 100)
            : 0;
        $scoringUser = new UserOutput;
        $scoringUser->setId($user->getId())
            ->setName($user->getName())
            ->setDialogs($data['dialogs_count'])
            ->setCallDialogs($data['call_dialogs_count'])
            ->setChatDialogs($data['chat_dialogs_count'])
            ->setScoringCall(round($callPoints, 2))
            ->setScoringChat(round($chatPoints, 2))
            ->setScoreCoveringCall($data['call_dialogs_count'] ? round(count($userCallDialogs) / $data['call_dialogs_count'] * 100, 2) : 0.0)
            ->setScoreCoveringChat($data['chat_dialogs_count'] ? round(count($userChatDialogs) / $data['chat_dialogs_count'] * 100, 2) : 0.0)
            ->setScoringPoints(($data['call_dialogs_count'] + $data['chat_dialogs_count']) ? round(($data['call_dialogs_count'] * $callPoints + $data['chat_dialogs_count'] * $chatPoints) / ($data['call_dialogs_count'] + $data['chat_dialogs_count']), 2) : 0.0)
            ->setScoring(round($scoring, 2))
            ->setScoringCount(count($userDialogs))
            ->setCriticalErrors($criticalErrors)
            ->setTotalScore(0);

        $scoringUser->setActiveTime(round($activeTimeTotal, 2));

        $slCall = round(100 - (($data['call_sl'] / ($data['call_dialogs_count'] ?: 1)) * 100), 2);
        $slChat = round(100 - (($data['chat_sl'] / ($data['chat_dialogs_count'] ?: 1)) * 100), 2);
        $scoringUser->setServiceLevelCall($slCall)
            ->setServiceLevelChat($slChat)
            ->setServiceLevel(($data['call_dialogs_count'] + $data['chat_dialogs_count']) ? round(($data['call_dialogs_count'] * $slCall + $data['chat_dialogs_count'] * $slChat) / ($data['call_dialogs_count'] + $data['chat_dialogs_count']), 2) : 0.0);

        $slasaCall = round(100 - (($data['call_slasa'] / ($data['call_dialogs_count'] ?: 1)) * 100), 2);
        $slasaChat = round(100 - (($data['chat_slasa'] / ($data['chat_dialogs_count'] ?: 1)) * 100), 2);
        $scoringUser->setServiceLevelAverageSpeedAnswerCall($slasaCall)
            ->setServiceLevelAverageSpeedAnswerChat($slasaChat)
            ->setServiceLevelAverageSpeedAnswer(($data['call_dialogs_count'] + $data['chat_dialogs_count']) ? round(($data['call_dialogs_count'] * $slasaCall + $data['chat_dialogs_count'] * $slasaChat) / ($data['call_dialogs_count'] + $data['chat_dialogs_count']), 2) : 0.0);

        $scoringUser->setKnowledge(round($knowledgeScoringTotal, 2));
        $scoringUser
            ->setTrainee($user->getCurrentRank(Role::OPERATOR(), $dialogs_date)?->isTrainee())
            ->setZone($user->getCurrentRank(Role::OPERATOR(), $dialogs_date)?->getZone());

        return $this->calcTotalScore($scoringUser);
    }

}