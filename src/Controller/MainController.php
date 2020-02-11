<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $response = new Response(
                '',
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );

            $response->headers->setCookie(Cookie::create('lastUsername', $this->getUser()->getUsername()));

            $response->prepare($request);
            $response->send();
        }

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

        //verify the existance of a username already used

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,]);
    }

    /**
     * @Route("/myprofil", name ="myprofil")
     */
    public function myprofil(EntityManagerInterface $entityManager, Request $request)
    {
        $user = new User();
        $password = new User();
        $user->setUsername($this->getUser()->getUsername());
        $user->setName($this->getUser()->getName());
        $user->setFirstname($this->getUser()->getFirstname());
        $user->setPhone($this->getUser()->getPhone());
        $user->setMail($this->getUser()->getMail());
        $user->setEstablishment($this->getUser()->getEstablishment());


        $profilForm = $this->createForm(MyUserType::class, $user);
        $profilForm->handleRequest($request);

        $passwordForm = $this->createForm(PasswordType::class);
        $passwordForm->handleRequest($request);


        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->redirectToRoute('main/home.html.twig', ['id' => $user->getId()]);
        }


        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $entityManager->persist($password);
            $entityManager->flush();

            $this->redirectToRoute('main/home.html.twig', ['id' => $password->getId()]);
        }

        $profilFormView = $profilForm->createView();
        $passwordFormView = $passwordForm->createView();

        return $this->render('main/myprofil.html.twig', compact('profilFormView', $user, 'passwordFormView', $password));

    }
}
