<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;


/**
 * Class SecurityController
 * @package App\Controller
 * @Route ("/security", name="security_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index()
    {
        return $this->render(
            'security/index.html.twig',
            [
                'controller_name' => 'SecurityController',
            ]
        );
    }

    /**
     * @Route ("/forgotten_password", name ="forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        if ($request->isMethod('POST')) {
            $mail = $request->request ->get('mail');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByMail($mail);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Mail inconnu');

                return $this->redirectToRoute('home');
            }
            $token = $tokenGenerator->generateToken();
            try {
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', $exception->getMessage());

                return $this->redirectToRoute('home');
            }

            $url = $this->generateUrl(
                'security_reset_password',
                array('token' => $token),
                UrlGeneratorInterface::ABSOLUTE_PATH
            );

            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('vianney.newgame@gmail.com')
                ->setTo($user->getMail())
                ->setBody(
                    "Bonjour, Voici le token pour reseter votre mot de passe : ".$url,
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('notice', 'Mail envoyé');

            return $this->redirectToRoute('home');

        }

        return $this->render("security/forgotten_password.html.twig");
    }

    /**
     * @Route("/reset_password/{token}", name ="reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Token inconnu');

                return $this->redirectToRoute('home');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('notice', 'Mot de passe mis à jour');

            return $this->redirectToRoute('home');

        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);

        }
    }

}
