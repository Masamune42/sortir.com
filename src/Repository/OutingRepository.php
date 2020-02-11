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

    public function findOutingForHome($user, $data)//$data are the data from the OutingHomeType form
    {
        //add 23 hours, 59 mins and 59 sec to dateMax to search for the whole day
        $data['dateMax']->add(new \DateInterval('PT23H59M59S'));

        //TODO add other fields
        $entityManager = $this->getEntityManager();
        $dql = <<<DQL
SELECT o
FROM App\Entity\Outing o
WHERE :dateMin < o.startTime AND :dateMax > o.startTime
DQL;
        $query = $entityManager->createQuery($dql);
        $query->setParameters([
            'dateMin' => $data['dateMin'],
            'dateMax' => $data['dateMax'],
        ]);

        return $query->getResult();
    }
}
