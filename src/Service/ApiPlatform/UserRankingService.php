<?php declare(strict_types=1);

namespace App\Service\ApiPlatform;

use App\ApiPlatform\DataProvider\SupervisorDataProvider;
use App\ApiPlatform\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Entity\UserRank;
use App\Entity\Zone;
use App\Enum\User\Role;
use App\Object\User\SupervisorOutput;
use App\Object\User\UserOutput;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class UserRankingService
{

    public function __construct(
        private UserDataProvider $dataProvider,
        private SupervisorDataProvider $supervisorDataProvider,
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function scoreUsers(?array $dates) {
        $rankedUsers = $this->dataProvider->getCollection(User::class, context: ['dialogs_date' => $dates]);
        $zones = $this->entityManager->getRepository(Zone::class)->createQueryBuilder('z')
            ->where('z.active = true')
            ->andWhere('z.type = :role')->setParameter('role', Role::OPERATOR)
            ->orderBy('z.rangeStart', 'ASC')
            ->getQuery()->getResult();
        $userPositions = [];
        $traineePositions = [];
        $rankingUsers = array_values(array_filter($rankedUsers, fn(UserOutput $u) => !$u->isTrainee()));
        $traineeUsers = array_values(array_filter($rankedUsers, fn(UserOutput $u) => $u->isTrainee()));
        /** @var UserOutput $userOutput */
        foreach ($rankingUsers as $position => $userOutput) {
            $userPositions[$position]['percent'] = ($position + 1) / count($rankingUsers) * 100;
            $prevPercent = isset($userPositions[$position - 1]) ? $userPositions[$position - 1]['percent'] : 0;
            $currentZoneHit = array_filter($zones, fn (Zone $zone) => $userPositions[$position]['percent'] >= $zone->getRangeStart() && $userPositions[$position]['percent'] <= $zone->getRangeEnd());
            $prevZone = array_filter($zones, fn (Zone $zone) => $prevPercent >= $zone->getRangeStart() && $prevPercent <= $zone->getRangeEnd());

            $potentialZones = array_slice(array_filter($zones, fn ($val, $key) => $key <= max(array_keys($currentZoneHit)), ARRAY_FILTER_USE_BOTH), max(array_keys($prevZone)));

            $resultZone = array_reduce($potentialZones, function($a, Zone $b){
                return is_null($a) ? $b : ($a->getPriority() >= $b->getPriority() ? $a : $b);
            });
            $userPositions[$position]['zone'] = $resultZone;
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->find($userOutput->getId());
            $currentRank = $user->getCurrentRank(Role::OPERATOR(), $dates);
            if (!$currentRank instanceof UserRank){
                $currentRank = new UserRank();
                $currentRank->setDate($dates['after'] ? (new DateTime($dates['after']))->modify('+1 second') : new DateTime())
                    ->setUser($user)
                    ->setType(Role::OPERATOR());
            }

            if (!empty($user->getWorkStartAt()) && $user->getWorkStartAt() > new DateTime()) {
                $currentRank->setTrainee(true);
            }

            $currentRank->setPosition($position + 1)
                ->setScore($userOutput->getTotalScore())
                ->setZone($resultZone);
            $this->entityManager->persist($currentRank);
        }
        foreach ($traineeUsers as $position => $userOutput) {
            $traineePositions[$position]['percent'] = ($position + 1) / count($traineeUsers) * 100;
            $prevPercent = isset($traineePositions[$position - 1]) ? $traineePositions[$position - 1]['percent'] : 0;
            $currentZoneHit = array_filter($zones, fn (Zone $zone) => $traineePositions[$position]['percent'] >= $zone->getRangeStart() && $traineePositions[$position]['percent'] <= $zone->getRangeEnd());
            $prevZone = array_filter($zones, fn (Zone $zone) => $prevPercent >= $zone->getRangeStart() && $prevPercent <= $zone->getRangeEnd());

            $potentialZones = array_slice(array_filter($zones, fn ($val, $key) => $key <= max(array_keys($currentZoneHit)), ARRAY_FILTER_USE_BOTH), max(array_keys($prevZone)));

            $resultZone = array_reduce($potentialZones, function($a, Zone $b){
                return is_null($a) ? $b : ($a->getPriority() >= $b->getPriority() ? $a : $b);
            });
            $traineePositions[$position]['zone'] = $resultZone;
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->find($userOutput->getId());
            $currentRank = $user->getCurrentRank(Role::OPERATOR(), $dates);
            if (!$currentRank instanceof UserRank){
                $currentRank = new UserRank();
                $currentRank->setDate($dates['after'] ? (new DateTime($dates['after']))->modify('+1 second') : new DateTime())
                    ->setUser($user)
                    ->setType(Role::OPERATOR());
            }

            if (!empty($user->getWorkStartAt()) && $user->getWorkStartAt() > new DateTime()) {
                $currentRank->setTrainee(true);
            }

            $currentRank->setPosition($position + 1)
                ->setScore($userOutput->getTotalScore())
                ->setZone($resultZone);
            $this->entityManager->persist($currentRank);
        }
        $this->entityManager->flush();
    }

    public function scoreSupervisors(?array $dates)
    {
        $rankedSupervisors = $this->supervisorDataProvider->getCollection(User::class, context: ['dialogs_date' => $dates]);
        $zones = $this->entityManager->getRepository(Zone::class)->createQueryBuilder('z')
            ->where('z.active = true')
            ->andWhere('z.type = :role')->setParameter('role', Role::SUPERVISOR)
            ->orderBy('z.rangeStart', 'ASC')
            ->getQuery()->getResult();
        $supervisorPositions = [];
        /** @var SupervisorOutput $supervisorOutput */
        foreach ($rankedSupervisors as $position => $supervisorOutput) {
            $supervisorPositions[$position]['percent'] = ($position + 1) / count($rankedSupervisors) * 100;
            $prevPercent = isset($supervisorPositions[$position - 1]) ? $supervisorPositions[$position - 1]['percent'] : 0;
            $currentZoneHit = array_filter($zones, fn (Zone $zone) => $supervisorPositions[$position]['percent'] >= $zone->getRangeStart() && $supervisorPositions[$position]['percent'] <= $zone->getRangeEnd());
            $prevZone = array_filter($zones, fn (Zone $zone) => $prevPercent >= $zone->getRangeStart() && $prevPercent <= $zone->getRangeEnd());
            $potentialZones = array_slice(array_filter($zones, fn ($val, $key) => $key <= max(array_keys($currentZoneHit)), ARRAY_FILTER_USE_BOTH), max(array_keys($prevZone)));
            $resultZone = array_reduce($potentialZones, function($a, Zone $b){
                return is_null($a) ? $b : ($a->getPriority() >= $b->getPriority() ? $a : $b);
            });
            $supervisorPositions[$position]['zone'] = $resultZone;
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->find($supervisorOutput->getId());
            $currentRank = $user->getCurrentRank(Role::SUPERVISOR(), $dates);
            if (!$currentRank instanceof UserRank){
                $currentRank = new UserRank();
                $currentRank->setDate($dates['after'] ? (new DateTime($dates['after']))->modify('+1 second') : new DateTime())
                    ->setUser($user)
                    ->setType(Role::SUPERVISOR());
            }
            $currentRank->setPosition($position + 1)
                ->setScore($supervisorOutput->getScoringRatio())
                ->setZone($resultZone);
            $this->entityManager->persist($currentRank);
        }
        $this->entityManager->flush();
    }
}