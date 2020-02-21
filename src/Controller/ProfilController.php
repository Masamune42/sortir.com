<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use App\Form\PasswordType;
use App\Service\UploadProfilPic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @Route("/profil", name ="profil_")
 */
class ProfilController extends AbstractController
{

    /**
     * @Route("/myprofil", name ="myprofil")
     */

    public function myprofil(
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        Request $request,
        UploadProfilPic $uploadProfilPic
    ) {
//        phpinfo();
        $user = $this->getUser();

        $encoder = $encoderFactory->getEncoder($user);


        $newp = $request->request->get('my_user')['newpassword']['first'];

        // dump($request);


        // Verification of the password to validate the change.
        $validPassword = $encoder->isPasswordValid(
            $user->getPassword(), // the encoded password
            $request->request->get('password'),       // the submitted password

            $user->getSalt()
        );

        $profilForm = $this->createForm(MyUserType::class, $user);

        $oldPicturePath = null;

        if ($user->getPicture()) {
            $pathdir = \dirname(__DIR__);
            $oldPicturePath = $pathdir.'/../public/images/profil/'.$user->getPicture();
            //die($oldPicturePath);
        }

        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid() && $validPassword) {
            if (!empty($newp)) {
                $user->setPassword($encoder->encodePassword($newp, $user->getSalt()));
            }

            $picture = $profilForm->get('picture')->getData();

            if ($picture) {
                $pictureName = $uploadProfilPic->upload($picture);
                $user->setPicture($pictureName);

                if ($oldPicturePath) {
                    unlink($oldPicturePath);
                }
            }

            $this->addFlash('success', 'Profil modifié !');

            $entityManager->persist($user);
            $entityManager->flush();

            $this->redirectToRoute('profil_myprofil');

        } else {
            if ($profilForm->isSubmitted() && !$validPassword) {
                $this->addFlash('error', 'Mot de passe erroné !');
            }
        }

        $profilFormView = $profilForm->createView();

        return $this->render(
            'main/myprofil.html.twig',
            compact('profilFormView', $user, 'user')
        );
    }

    /**
     * @Route("/{id}", name="byid", requirements={"id" : "\d+"})
     */
    public function profil(User $user)
    {
        return $this->render('profil/profils.html.twig', compact('user'));
    }

    /**
     * @Route("/deletepicture/{id}", name="delete_pic", requirements={"id" : "\d+"})
     */
    public function delete(User $user, EntityManagerInterface $entityManager)
    {
        $pathdir = \dirname(__DIR__);
        $oldPicturePath = $pathdir.'/../public/images/profil/'.$user->getPicture();
        unlink($oldPicturePath);

        $user->setPicture(null);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute("profil_myprofil");

    }

}