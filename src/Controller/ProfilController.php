<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ProfilController extends AbstractController
{

    /**
     * @Route("/myprofil", name ="myprofil")
     */
    public function myprofil(EntityManagerInterface $entityManager,EncoderFactoryInterface $encoderFactory, Request $request)
    {
        $user=$this->getUser();



        $encoder = $encoderFactory->getEncoder($user);



        $newpassword=$this->getUser();
        dump($request);
dump($request->request->get('password'));

        $validPassword = $encoder->isPasswordValid( $user->getPassword(), // the encoded password
            $request->request->get('password'),       // the submitted password
            $user->getSalt());



        $profilForm = $this->createForm(MyUserType::class, $user);
        $profilForm->handleRequest($request);

        $passwordForm = $this->createForm(PasswordType::class, $newpassword);
        $passwordForm->handleRequest($request);



        if ($profilForm->isSubmitted() && $profilForm->isValid() && $validPassword ) {
            dump($user);

            $entityManager->persist($user);
            $entityManager->flush();



            $this->redirectToRoute('myprofil');
        }


        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            dump($newpassword);
            $entityManager->persist($newpassword);
            $entityManager->flush();

            $this->redirectToRoute('myprofil');
        }

        $profilFormView = $profilForm->createView();
        $passwordFormView = $passwordForm->createView();

        return $this->render(
            'main/myprofil.html.twig',
            compact('profilFormView', $user, 'passwordFormView', $newpassword)
        );
    }

        /**
         * @Route("/profils/{id}", name="profils")
         */
        public
        function profils($id, entityManagerInterface $entityManager)
        {
            $userRepository = $entityManager->getRepository(User::class);

            $user = $userRepository->find($id);

            return $this->render('profil/profils.html.twig', compact('user'));
        }


}