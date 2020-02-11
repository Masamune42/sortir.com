<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/myprofil", name ="myprofil")
     */
    public function myprofil(EntityManagerInterface $entityManager, Request $request)
    {
        $user = new User();
        $profilForm = $this->createForm(MyUserType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->redirectToRoute('main/home.html.twig', ['id' => $user->getId()]);
        }

        $profilFormView = $profilForm->createView();

        return $this->render('main/myprofil.html.twig', compact('profilFormView'));

    }

    /**
     * @Route("/profils/{id}", name="profils")
     */
    public function profils($id, entityManagerInterface $entityManager)
    {
        $userRepository = $entityManager->getRepository(User::class);

        $user = $userRepository->find($id);

        return $this->render('profil/profils.html.twig', compact('user'));
    }
}
