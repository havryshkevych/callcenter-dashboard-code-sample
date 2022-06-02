<?php declare(strict_types=1);

namespace App\Event\Listener;

use App\Entity\ActiveTime;
use App\Entity\Evaluation;
use App\Service\ApiPlatform\UserRankingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RankChangeListener
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected UserRankingService     $rankingService,
    )
    {
    }

    public function postPersist($entity)
    {
        $this->processUpdate($entity);
    }

    public function postUpdate($entity)
    {
        $this->processUpdate($entity);
    }

    protected function processUpdate($entity)
    {
        $date = new DateTime();

        if (property_exists($entity, 'date')) {
            $date = $entity->getDate();
        }
        if ($entity::class === Evaluation::class) {
            /** @var Evaluation $entity */
            $date = $entity->getScoring()->getDialog()->getDate();
        }
        $dates = [
            "after" => $date->modify('midnight first day of this month')->format("Y-m-d H:i:s"),
            "before" => $date->modify('midnight first day of next month')->format("Y-m-d H:i:s")
        ];
        if ($entity::class === Evaluation::class || $entity::class === ActiveTime::class) {
            $this->updateSupervisorRankings($dates);
        }

        $this->updateRanks($dates);
    }

    protected function updateSupervisorRankings(array $dates)
    {
        try {
            $this->rankingService->scoreSupervisors($dates);
        } catch (Exception $e) {
            //ignore
        }
    }

    protected function updateRanks(array $dates)
    {
        try {
            $this->rankingService->scoreUsers($dates);
        } catch (Exception $e) {
            //ignore
        }
    }
}
