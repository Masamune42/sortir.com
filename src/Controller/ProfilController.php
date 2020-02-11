<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyUserType;
use App\Form\PasswordType;
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

        return $this->render(
            'main/myprofil.html.twig',
            compact('profilFormView', $user, 'passwordFormView', $password)
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