<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        //get the login error if there is one

        $error = $authenticationUtils->getLastAuthenticationError();

        //last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();



        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,]);
    }

    /**
     * @Route("/profil", name ="profil")
     */
    public function profil(EntityManagerInterface $entityManager, Request $request)
    {
        $user = new User();
        $profilForm = $this->createForm(UserType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->redirectToRoute('main/home.html.twig', ['id' => $user->getId()]);
        }

        $profilFormView = $profilForm->createView();

        return $this->render('main/profil.html.twig', compact('profilFormView'));

    }
}
