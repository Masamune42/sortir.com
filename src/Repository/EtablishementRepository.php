<?php

namespace App\Repository;

use App\Entity\Etablishement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Etablishement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etablishement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etablishement[]    findAll()
 * @method Etablishement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtablishementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etablishement::class);
    }

    // /**
    //  * @return Etablishement[] Returns an array of Etablishement objects
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
    public function findOneBySomeField($value): ?Etablishement
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
