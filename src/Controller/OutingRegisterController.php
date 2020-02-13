<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Outing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OutingRegisterController extends AbstractController
{
    /**
     * @Route("/outing/register", name="outing_register")
     */
    public function register(EntityManagerInterface $entityManager, $id)
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);

        $outing->addParticipant($user);

        return $this->render('outing_register/index.html.twig', [
            'controller_name' => 'OutingRegisterController',
        ]);
    }

    public function remove(EntityManagerInterface $entityManager, $id)
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);

        $outing->remove($user);

        return $this->render('outing_register/index.html.twig', [
            'controller_name' => 'OutingRegisterController',
        ]);
    }
}
