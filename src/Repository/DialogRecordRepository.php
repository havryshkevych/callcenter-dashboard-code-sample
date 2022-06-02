<?php

namespace App\Repository;

use App\Entity\DialogRecord;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DialogRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method DialogRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method DialogRecord[]    findAll()
 * @method DialogRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DialogRecord::class);
    }

    public function findByChatId(string $value, string $clientId = null)
    {
        $params = [
            'chatId' => $value
        ];
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.chatId = :chatId');
        if ($clientId !== null) {
            $qb->orWhere('d.clientId = :clientId AND d.dialog IS NULL AND d.receivedAt >= :time');
            $params['clientId'] = $clientId;
            $params['time'] = (new DateTime('8 hour ago'))->format("Y-m-d H:i:s");
        }
        return $qb->orderBy('d.createdAt', 'ASC')
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }

    public function findOneByChatId(string $value, string $clientId = null, $order = 'ASC'): ?DialogRecord
    {
        $params = [
            'chatId' => $value
        ];
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.chatId = :chatId');

        if ($clientId !== null) {
            $qb->orWhere('d.clientId = :clientId AND d.dialog IS NULL');
            $params['clientId'] = $clientId;
        }

        return $qb->orderBy('d.createdAt', $order)
            ->setParameters($params)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneTodayByClientId(string $value): ?DialogRecord
    {
        $query = $this->createQueryBuilder('d');

        return $query->andWhere('d.clientId = :client_id')
            ->andWhere('d.createdAt >= :dialog_time')
            ->setParameters(['client_id' => $value,
                'dialog_time' => new DateTime('8 hour ago')])
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
