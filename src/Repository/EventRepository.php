<?php

namespace App\Repository;

use App\Entity\Event;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllByDateDesc()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFourNext()
    {
        $date = new DateTime('now');
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT
            *,
            (
            SELECT
            COUNT(s.user_id)
            FROM subscription s
            WHERE s.event_id = e.id
            ) AS nbusers
        FROM event e
        WHERE e.date > :now
        ORDER BY e.date ASC
        LIMIT 4
        ';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['now' => $date->format('Y-m-d')]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function findTwoNext()
    {
        $date = new DateTime('now');
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT
            *,
            (
            SELECT
            COUNT(s.user_id)
            FROM subscription s
            WHERE s.event_id = e.id
            ) AS nbusers
        FROM event e
        WHERE e.date > :now
        ORDER BY e.date ASC
        LIMIT 2
        ';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['now' => $date->format('Y-m-d')]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function findAllNext()
    {
        $date = new DateTime('now');
        return $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->setParameter('date', $date)
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNext()
    {
        $date = new DateTime('now');
        $twoNextEvents = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->andWhere('e.type = 1')
            ->setParameter('date', $date)
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult()
        ;
        // Are the subscriptions closed for the first event ? If so, take the second one.
        $stringDate = $twoNextEvents[0]->getEndSubs();
        $newDate =  clone $twoNextEvents[0]->getDate();
        $newDate->sub(new DateInterval('P'.$stringDate.'D'));
        if ($date < $newDate)
        {
            $nbusers = $this->createQueryBuilder('e')
                ->select('COUNT(s.user)')
                ->leftJoin('e.subscriptions', 's')
                ->andWhere('s.event = :event')
                ->setParameter('event', $twoNextEvents[0])
                ->getQuery()
                ->getOneOrNullResult()
            ;
            $res = ['0' => $twoNextEvents[0], 'nbusers' => $nbusers['1']];
        }
        else
        {
            $nbusers = $this->createQueryBuilder('e')
                ->select('COUNT(s.user)')
                ->leftJoin('e.subscriptions', 's')
                ->andWhere('s.event = :event')
                ->setParameter('event', $twoNextEvents[1])
                ->getQuery()
                ->getOneOrNullResult()
            ;
            $res = ['0' => $twoNextEvents[1], 'nbusers' => $nbusers['1']];
        }
        return $res;
    }

    public function findNextKid()
    {
        $date = new DateTime('now');
        $twoNextEvents = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->andWhere('e.type = 0')
            ->setParameter('date', $date)
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult()
        ;
        // Are the subscriptions closed for the first event ? If so, take the second one.
        $stringDate = $twoNextEvents[0]->getEndSubs();
        $newDate =  clone $twoNextEvents[0]->getDate();
        $newDate->sub(new DateInterval('P'.$stringDate.'D'));
        if ($date < $newDate)
        {
            $nbusers = $this->createQueryBuilder('e')
                ->select('COUNT(s.user)')
                ->leftJoin('e.subscriptions', 's')
                ->andWhere('s.event = :event')
                ->setParameter('event', $twoNextEvents[0])
                ->getQuery()
                ->getOneOrNullResult()
            ;
            $res = ['0' => $twoNextEvents[0], 'nbusers' => $nbusers['1']];
        }
        else
        {
            $nbusers = $this->createQueryBuilder('e')
                ->select('COUNT(s.user)')
                ->leftJoin('e.subscriptions', 's')
                ->andWhere('s.event = :event')
                ->setParameter('event', $twoNextEvents[1])
                ->getQuery()
                ->getOneOrNullResult()
            ;
            $res = ['0' => $twoNextEvents[1], 'nbusers' => $nbusers['1']];
        }
        return $res;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
