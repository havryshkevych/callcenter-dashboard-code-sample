<?php declare(strict_types=1);

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\DateFilterInterface;
use App\Entity\ActiveTime;
use App\Entity\Dialog;
use App\Entity\KnowledgeScoring;
use App\Entity\Scoring;
use App\Entity\User;
use App\Enum\Dialog\Type;
use App\Enum\User\Role;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements DateFilterInterface
{
    private array $operatorValue = [
        self::PARAMETER_BEFORE => 'lte',
        self::PARAMETER_STRICTLY_BEFORE => 'lt',
        self::PARAMETER_AFTER => 'gte',
        self::PARAMETER_STRICTLY_AFTER => 'gt',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByCallId(string $value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.callId = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByChatId(string $value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.chatId = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function buildRanking(QueryBuilder $qb, $dialogsDates = [], $trainee = false)
    {
        $params = [
            'type_call' => Type::CALL,
            'type_chat' => Type::CHAT,
            'now' => (new DateTime())->format("Y-m-d H:i:s"),
            'supervisorRole' => '%'.Role::SUPERVISOR.'%'
        ];

        $qb
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_call THEN d.id ELSE NULLIF(1,1) END)) call_dialogs_count')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_chat THEN d.id ELSE NULLIF(1,1) END)) chat_dialogs_count')
            ->addSelect('COUNT(DISTINCT d.id) as dialogs_count')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_call THEN s.id ELSE NULLIF(1,1) END)) call_scoring_count')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_chat THEN s.id ELSE NULLIF(1,1) END)) chat_scoring_count')
            ->addSelect('COUNT(DISTINCT s.id) as scoring_count')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_call AND d.serviceLevelWarning = true THEN d.id ELSE NULLIF(1,1) END)) call_sl')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_chat AND d.serviceLevelWarning = true THEN d.id ELSE NULLIF(1,1) END)) chat_sl')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_call AND d.serviceLevelAverageAnswerSpeedWarning = true THEN d.id ELSE NULLIF(1,1) END)) call_slasa')
            ->addSelect('COUNT(DISTINCT (CASE WHEN d.type = :type_chat AND d.serviceLevelAverageAnswerSpeedWarning = true THEN d.id ELSE NULLIF(1,1) END)) chat_slasa')
        ;

        $traineeExpression = $qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->lte('u.workStartAt', ':now'),
                $qb->expr()->gte('d.date', 'u.workStartAt')
            ),
            $qb->expr()->andX(
                $qb->expr()->gte('u.workStartAt', ':now'),
                $qb->expr()->lte('d.date', 'u.workStartAt')
            )
        );
        if (empty($dialogsDates)) {
            $qb->leftJoin('u.dialogs',
                'd',
                Expr\Join::WITH, (string)
                $qb->expr()->andX(
                    $qb->expr()->between('d.date', ":first_day", ":last_day"),
                    $traineeExpression
                )
            );
            $params['first_day'] = (new DateTime('midnight first day of this month'))->format("Y-m-d H:i:s");
            $params['last_day'] = (new DateTime('midnight first day of next month'))->format("Y-m-d H:i:s");
        } else {
            $dateExpr = [];
            foreach ($dialogsDates as $operator => $date) {
                $strDate = (new DateTime($date))->format("Y-m-d H:i:s");
                $dateExpr[] = $qb->expr()->{$this->operatorValue[$operator]}('d.date', "'".$strDate."'");
            }
            $qb->leftJoin('u.dialogs',
                'd',
                Expr\Join::WITH, (string)
            $qb->expr()->andX(
                $qb->expr()->andX(...$dateExpr),
                $traineeExpression
            ));
        }

        $qb->leftJoin('d.scoring', 's', Expr\Join::WITH, (string)$qb->expr()->eq('s.user', 'u.id'))
            ->leftJoin('s.evaluations', 'e')
            ->leftJoin('u.activeTimes', 'at')
            ->leftJoin('u.knowledgeScorings', 'ks')
            ->groupBy('u.id')
            ->andWhere($qb->expr()->eq('u.active', true))
            ->andWhere('u.roles not like :supervisorRole')
            ->setParameters($params);
    }

    public function buildSupervisorsRanking($dialogsDates = [])
    {
        $params = [
            'now' => (new DateTime())->format("Y-m-d H:i:s"),
        ];
        $qb = (new QueryBuilder($this->_em));
        $qb->from(Scoring::class, 's')
            ->select('s.id')
            ->addSelect('IDENTITY(u.supervisor) as supervisor')
            ->addSelect('sum(e.value) as sum')
            ->addSelect('CASE WHEN SUM((CASE WHEN e.value > 0 AND ec.critical > 0 THEN 1 ELSE 0 END)) > 0 THEN 0 ELSE SUM(e.value) END as points')
            ->addSelect('SUM(CASE WHEN e.value > 0 AND ec.critical > 0 THEN 1 ELSE NULLIF(1,1) END) as critical_errors')
        ;

        $qb->leftJoin('s.dialog','d')
            ->leftJoin('d.users', 'u');

        $traineeExpression = $qb->expr()->andX(
                $qb->expr()->lte('u.workStartAt', ':now'),
                $qb->expr()->gte('d.date', 'u.workStartAt'),
            );
        if (empty($dialogsDates)) {
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->between('d.date', ":first_day", ":last_day"),
                $traineeExpression
            ));
            $params['first_day'] = (new DateTime('midnight first day of this month'))->format("Y-m-d H:i:s");
            $params['last_day'] = (new DateTime('midnight first day of next month'))->format("Y-m-d H:i:s");
        } else {
            $dateExpr = [];
            foreach ($dialogsDates as $operator => $date) {
                $strDate = (new DateTime($date))->format("Y-m-d H:i:s");
                $dateExpr[] = $qb->expr()->{$this->operatorValue[$operator]}('d.date', "'".$strDate."'");
            }
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->andX(...$dateExpr),
                $traineeExpression
            ));
        }

        $qb->leftJoin('s.evaluations', 'e')
            ->leftJoin('e.criteria', 'ec')
            ->groupBy('s.id, u.id')
            ->andWhere($qb->expr()->eq('u.active', true))
            ->andWhere($qb->expr()->isNotNull('u.supervisor'))
            ->setParameters($params);

        return $qb->getQuery()->getResult();
    }
    
    public function dialogsScoring($dialogsDates = []): mixed
    {
        $params = [
            'now' => (new DateTime())->format("Y-m-d H:i:s")
        ];
        $qb = (new QueryBuilder($this->_em));
        $calc = $qb
            ->select([
                'dialog.id as dialog_id',
                'dialog.type'
            ])
            ->addSelect('u.id as user_id')
            ->addSelect('SUM(e.value) points_scored')
            ->addSelect('SUM((CASE WHEN e.value > 0 AND ec.critical > 0 THEN 1 ELSE NULLIF(1,1) END)) as critical_errors')
            ->addSelect('CASE WHEN SUM((CASE WHEN e.value > 0 AND ec.critical > 0 THEN 1 ELSE 0 END)) > 0 THEN 0 ELSE SUM(e.value) END as points')
            ->from(Dialog::class, 'dialog')
            ->join('dialog.users', 'u')
            ->join('dialog.scoring', 's', Expr\Join::WITH, (string)$qb->expr()->eq('s.user', 'u.id'))
            ->join('s.evaluations', 'e')
            ->join('e.criteria', 'ec');

        if (empty($dialogsDates)) {
            $calc->andWhere(
                $calc->expr()->between('dialog.date', ":first_day", ":last_day")
            );
            $params['first_day'] = (new DateTime('midnight first day of this month'))->format("Y-m-d H:i:s");
            $params['last_day'] = (new DateTime('midnight first day of next month'))->format("Y-m-d H:i:s");
        } else {
            $dateExpr = [];
            foreach ($dialogsDates as $operator => $date) {
                $strDate = (new DateTime($date))->format("Y-m-d H:i:s");
                $dateExpr[] = $calc->expr()->{$this->operatorValue[$operator]}('dialog.date', "'".$strDate."'");
            }
            $calc->andWhere((string)$calc->expr()->andX(...$dateExpr));
        }

        $calc
            ->andWhere((string)$calc->expr()->orX(
            (string)$calc->expr()->andX(
                $calc->expr()->lte('u.workStartAt', ':now'),
                $calc->expr()->gte('dialog.date', 'u.workStartAt')
            ),
            (string)$calc->expr()->andX(
                $calc->expr()->gte('u.workStartAt', ':now'),
                $calc->expr()->lte('dialog.date', 'u.workStartAt')
            )
        ))
            ->andWhere($calc->expr()->eq('u.active', true))
            ->setParameters($params);

        $calc->groupBy('dialog.id, u.id');

        return $calc->getQuery()->getResult();
    }

    public function activeTime($dates = [])
    {
        $params = [];
        $calc = (new QueryBuilder($this->_em))
            ->select([
                'IDENTITY(at.user) user_id',
                'SUM(at.seconds) total_seconds'
            ])
            ->from(ActiveTime::class, 'at')
            ->join('at.user', 'u');

        if (empty($dates)) {
            $calc->andWhere(
                $calc->expr()->between('at.date', ":first_day", ":last_day")
            );
            $params['first_day'] = (new DateTime('midnight first day of this month'))->format("Y-m-d H:i:s");
            $params['last_day'] = (new DateTime('midnight first day of next month'))->format("Y-m-d H:i:s");
        } else {
            $dateExpr = [];
            foreach ($dates as $operator => $date) {
                $strDate = (new DateTime($date))->format("Y-m-d H:i:s");
                $dateExpr[] = $calc->expr()->{$this->operatorValue[$operator]}('at.date', "'".$strDate."'");
            }
            $calc->andWhere((string)$calc->expr()->andX(...$dateExpr));
        }

        $calc
            ->andWhere($calc->expr()->eq('u.active', true))
            ->setParameters($params);

        $calc->groupBy('at.user');

        return $calc->getQuery()->getResult();
    }

    public function knowledge($dates = [])
    {
        $params = [];
        $calc = (new QueryBuilder($this->_em))
            ->select([
                'IDENTITY(ks.user) user_id',
                'SUM(ks.result * ks.coefficient) total_score'
            ])
            ->from(KnowledgeScoring::class, 'ks')
            ->join('ks.user', 'u');

        if (empty($dates)) {
            $calc->andWhere(
                $calc->expr()->between('ks.date', ":first_day", ":last_day")
            );
            $params['first_day'] = (new DateTime('midnight first day of this month'))->format("Y-m-d H:i:s");
            $params['last_day'] = (new DateTime('midnight first day of next month'))->format("Y-m-d H:i:s");
        } else {
            $dateExpr = [];
            foreach ($dates as $operator => $date) {
                $strDate = (new DateTime($date))->format("Y-m-d H:i:s");
                $dateExpr[] = $calc->expr()->{$this->operatorValue[$operator]}('ks.date', "'".$strDate."'");
            }
            $calc->andWhere((string)$calc->expr()->andX(...$dateExpr));
        }

        $calc
            ->andWhere($calc->expr()->eq('u.active', true))
            ->setParameters($params);

        $calc->groupBy('ks.user');

        return $calc->getQuery()->getResult();
    }
}
