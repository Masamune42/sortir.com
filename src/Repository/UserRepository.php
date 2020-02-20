<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User|null findOneByMail($mail)
 * @method User|null findOneByResetToken($token)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    // /*

    public function findByUsernameOrMail($username, $mail)
    {
        $qb = $this->createQueryBuilder('u')
            ->where("u.username = :username")
            ->setParameter('username', $username)
            ->orWhere("u.mail = :mail")
            ->setParameter('mail', $mail);

        return $qb->getQuery()->getResult();
    }

    public function findAllUsernames()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.username');

        $result = $qb->getQuery()->getResult();


        return array_column($result, 'username');
    }

    public function findAllMails()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.mail');

        $result = $qb->getQuery()->getResult();

        return array_column($result, 'mail');
    }

    // /**

    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
