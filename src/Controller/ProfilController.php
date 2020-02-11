<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profils/{id}", name="profils")
     */
    public function profils($id, entityManagerInterface  $entityManager)
    {
        $userRepository=$entityManager->getRepository(User::class);

        $user = $userRepository->find($id);

        return $this->render('profil/profils.html.twig', compact('user'));
    }
}
