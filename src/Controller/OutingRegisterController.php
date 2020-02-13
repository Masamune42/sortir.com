<?php

namespace App\Controller;

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
        $user = $this->getUser();

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);

        $entityManager->remove();

        return $this->render('outing_register/index.html.twig', [
            'controller_name' => 'OutingRegisterController',
        ]);
    }

    public function remove(EntityManagerInterface $entityManager, $id)
    {
        $user = $this->getUser();

        $outingRepository = $entityManager->getRepository(Outing::class);

        return $this->render('outing_register/index.html.twig', [
            'controller_name' => 'OutingRegisterController',
        ]);
    }
}
