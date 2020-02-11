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


        $qb = $this->createQueryBuilder('o')
            ->where("o.establishment = :establishment")
            ->setParameter('establishment', $data['establishment']);

        if ($data['nameContent'] !== null) {
            $qb->andWhere("o.name LIKE :nameContent");
            $qb->setParameter('nameContent', $data['nameContent']);
        }

        if ($data['dateMin'] !== null) {
            $qb->andWhere("o.startTime > :dateMin");
            $qb->setParameter('dateMin', $data['dateMin']);
        }

        if ($data['dateMax'] !== null) {
            //add 23 hours, 59 mins and 59 sec to dateMax to search for the whole day
            $data['dateMax']->add(new \DateInterval('PT23H59M59S'));
            $qb->andWhere("o.startTime > :dateMax");
            $qb->setParameter('dateMax', $data['dateMax']);
        }

        if ($data['iAmOrganizer']) {
            $qb->andWhere("o.organizer = :organizer");
            $qb->setParameter('organizer', $user);
        }

        if ($data['iAmRegistred']) {
            //TODO test carefully
            $qb->join("o.participant", "p");
            $qb->andWhere("p = :user");
            $qb->setParameter('user', $user);
        }

        if ($data['iAmNotRegistred']) {
            //TODO test carefully
            $qb->andWhere($qb->expr()->notIn('o.participant.id', $user->getId()));
        }

        if ($data['passedOuting']) {
            $qb->andWhere("o.startTime < :now");
        } else {
            $qb->andWhere("o.startTime > :now");
        }
        $now = new \DateTime('now');
        $qb->setParameter('now', $now);

        $qb->orderBy('o.startTime', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
