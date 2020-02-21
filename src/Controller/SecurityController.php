<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


/**
 * Class SecurityController
 * @package App\Controller
 * @Route ("/security", name="security_")
 */
class SecurityController extends AbstractController
{
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
            $mail = $request->request->get('mail');

            //Search the mail in the database
            //if exist continue
            //else mail not exist
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByMail($mail);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Mail inconnu');

                return $this->redirectToRoute('outing_home');
            }
            $token = $tokenGenerator->generateToken();
            try {
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', $exception->getMessage());

                return $this->redirectToRoute('outing_home');
            }

            //Generate an url with the token
            $url = $this->generateUrl(
                'security_reset_password',
                array('token' => $token),
                UrlGeneratorInterface::ABSOLUTE_URL
            );


            //Create mail for the user with the link for reset password
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('sortircom.noreply@gmail.com')
                ->setTo($user->getMail())
                ->setBody(
                    "Bonjour, Voici le token pour reseter votre mot de passe : <a href='$url'>$url</a>",
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Mail envoyé');

            return $this->redirectToRoute('outing_home');

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

                return $this->redirectToRoute('outing_home');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('outing_home');

        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);

        }
    }

}
