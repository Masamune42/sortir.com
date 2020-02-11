<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        dump($request);

        dump($request->request->get('_remember_me'));
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER') && $request->request->get('_remember_me') == 1) {

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
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session)
    {

        dump($request->query->get('rememberMe'));
        //get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        //last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //verify the existance of a username already used

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,]);
    }


}
