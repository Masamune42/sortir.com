<?php

namespace App\Repository;

use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findOutingForHome(
        $user,
        $establishment,
        $nameContent,
        $dateMin,
        $dateMax,
        $iAmOrganizer,
        $iAmRegistred,
        $iAmNotRegistred,
        $pastOuting
    ) {
        //TODO add other fields
        $entityManager = $this->getEntityManager();
        $dql = <<<DQL
SELECT o
FROM App\Entity\Outing o
WHERE :dateMin < o.startTime AND :dateMax > o.startTime
DQL;
        $query = $entityManager->createQuery($dql);
        $query->setParameter(
            compact(
                'dateMin',
                'dateMax'
            )
        );

        return $query->getResult();
    }



    // /**
    //  * @return Outing[] Returns an array of Outing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Outing
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
