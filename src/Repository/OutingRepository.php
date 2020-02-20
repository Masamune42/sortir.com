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

        $qb = $this->createQueryBuilder('o');

        if ($data['establishment'] !== null) {
            $qb->where("o.establishment = :establishment");
            $qb->setParameter('establishment', $data['establishment']);
        }

//        if ($data['nameContent'] !== null) {
//            $qb->andWhere("o.name LIKE :nameContent");
//            $qb->setParameter('nameContent', '%'.$data['nameContent'].'%');
//        }

        if ($data['dateMin'] !== null) {
            $qb->andWhere("o.startTime > :dateMin");
            $qb->setParameter('dateMin', $data['dateMin']);
        }

        if ($data['dateMax'] !== null) {
            //add 23 hours, 59 mins and 59 sec to dateMax to search for the whole day
            $data['dateMax']->add(new \DateInterval('PT23H59M59S'));
            $qb->andWhere("o.startTime < :dateMax");
            $qb->setParameter('dateMax', $data['dateMax']);
        }

        if ($data['iAmOrganizer']) {
            $qb->andWhere("o.organizer = :organizer2");
            $qb->setParameter('organizer2', $user);
        }

        $now = new \DateTime('now');
        if ($data['passedOuting']) {
            $oneMonthBeforeNow = clone($now);
            $oneMonthBeforeNow->sub(new \DateInterval('P1M'));//remove one month
            $qb->andWhere("o.startTime > :oneMonthBeforeNow"); //the outings older than 1 month are not selected
            $qb->setParameter('oneMonthBeforeNow', $oneMonthBeforeNow);
            $qb->andWhere("o.startTime < :now");
        } else {
            $qb->andWhere("o.startTime > :now");
        }
        $qb->setParameter('now', $now);


        $qb->orderBy('o.startTime', 'ASC');

        $rawresults = $qb->getQuery()->getResult();

        //filter on I am or I am not Registred
        if ($data['iAmRegistred'] xor $data['iAmNotRegistred']) { //exclusive OR

            if ($data['iAmRegistred']) {
                $results = array_filter(
                    $rawresults,
                    //callback function for the array filter
                    function ($outing) use ($user
                    ) { //use ($user) so that the $user can be used in the callback function
                        //return true if the current user is in the participant list, so the outing is kept by the filter
                        return in_array($user, $outing->getParticipant()->toArray());
                    }
                );
            }

            if ($data['iAmNotRegistred']) {
                $results = array_filter(
                    $rawresults,
                    function ($outing) use ($user) {
                        return !in_array($user, $outing->getParticipant()->toArray());
                    }
                );
            }
        } else {
            $results = $rawresults;
        }


        return array_filter( //filter on status (to be coded in dql instead)
            $results,
            function ($outing) use ($user) {
                return (
                    (($outing->getStatus()->getNameTech() == 'published')
                        || ($outing->getStatus()->getNameTech() == 'draft' && $outing->getOrganizer() == $user)
                        || ($outing->getStatus()->getNameTech() == 'canceled'
                            && (in_array($user, $outing->getParticipant()->toArray()) || ($outing->getOrganizer(
                                    ) == $user || $user->getAdministrator()))))
                    && ($outing->getUsersGroup() == null
                        || in_array( //the outing have to be open to all or I have to be a member of the group
                            $user,
                            $outing->getUsersGroup()->getParticipants()->toArray()
                        ))//get canceled outings only if I'm administator, organizer, or participant
                );
            }
        );
    }
}
