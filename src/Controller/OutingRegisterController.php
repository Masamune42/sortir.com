<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Outing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/outing", name="outing_", requirements={"id": "\d+"})
 */
class OutingRegisterController extends AbstractController
{
    /**
     * @Route("/register/{id}", name="register", requirements={"id": "\d+"})
     */
    public function register(EntityManagerInterface $entityManager, $id)
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);

        $outing->addParticipant($user);

        $entityManager->persist($outing);
        $entityManager->flush();



        return $this->redirectToRoute('outing_home');
    }

    /**
     * @Route("/remove/{id}", name="remove", requirements={"id": "\d+"})
     */
    public function remove(EntityManagerInterface $entityManager, $id)
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);

        $outing->removeParticipant($user);

        $entityManager->persist($outing);
        $entityManager->flush();


        return $this->redirectToRoute('outing_home');
    }
}
