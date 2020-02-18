<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session)
    {

        //get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        //last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //verify the existance of a username already used

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,]);
    }


    /**
     * @Route("/deactivated", name="deactivated")
     */
    public function deactivated(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session)
    {
        return $this->render('main/deactivated.html.twig');
    }

    /**
     * @Route("/redictionafterlogin", name="redirection_after_login")
     */
    public function redirection(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session)
    {
        $user = $this->getUser();

        if ($user->getActive()){
            return $this->redirectToRoute('outing_home');
        } else {
            return $this->redirectToRoute('deactivated');
        }
    }

}
