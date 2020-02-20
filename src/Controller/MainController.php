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
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->redirectToRoute('outing_home');
    }

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
    public function deactivated(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session, \Swift_Mailer $mailer)
    {
        if($request->isMethod('post')){
            $mail = $this->getUser()->getMail();
            $pseudo = $this->getUser()->getUsername();
            $body = $request->get('message');


            //Create mail for the user with the link for reset password
            $message = (new \Swift_Message('Message d\'un compte désactivé'))
                ->setFrom($mail)
                ->setTo('sortircom.noreply@gmail.com')
                ->setBody(
                    "L'utilisateur $pseudo vous a envoyé un message concernant la désactivation de son compte : <br>
                    $body",
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Mail envoyé');

            return $this->redirectToRoute('deactivated');

        }

        return $this->render('main/deactivated.html.twig');
    }

    /**
     * @Route("/redictionafterlogin", name="redirection_after_login")
     */
    public function redirection(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session)
    {
        $user = $this->getUser();

        if ($user->getActive()) {
            return $this->redirectToRoute('outing_home');
        } else {
            return $this->redirectToRoute('deactivated');
        }
    }

    /**
     * @Route("/easteregg", name="easter_egg")
     */
    public function easterEgg() {
        return $this->render('egg/egg.html.twig');
    }


}
