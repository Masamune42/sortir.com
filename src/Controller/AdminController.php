<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\User;
use App\Form\CSVType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController
 * @Route ("/admin", name = "admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): \Symfony\Component\HttpFoundation\Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $user->setAdministrator(false);
            $user->setActive(true);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('outing_home');
        }

        return $this->render('admin/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/csvadd", name="csvadd")
     */
    public function csvadd(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $csvForm = $this->createForm(CSVType::class);

        $csvForm->handleRequest($request);

        if ($csvForm->isSubmitted() && $csvForm->isValid()) {

            $file = $csvForm['csv_file']->getData()->openFile();

            $reader = Reader::createFromFileObject($file)
                ->setHeaderOffset(0);

            $estabishmentRepository = $entityManager->getRepository(Establishment::class);

            $correctData = true;
            foreach ($reader as $record) {

                if(!isset($record['password'])
                    || !isset($record['username'])
                    || !isset($record['firstname'])
                    || !isset($record['name'])
                    || !isset($record['phone'])
                    || !isset($record['mail'])
                    || !isset($record['administrator'])
                    || !isset($record['active'])
                    || !isset($record['establishment'])
                ){
                    $correctData = false;
                    break;
                }

                $user = new User();
                $password = $record['password'];
                $encodedPassword = $encoder->encodePassword($user, $password);
                $user->setUsername($record['username'])
                    ->setPassword($encodedPassword)
                    ->setFirstname($record['firstname'])
                    ->setName($record['name'])
                    ->setPhone($record['phone'])
                    ->setMail($record['mail'])
                    ->setAdministrator($record['administrator'] == 1)
                    ->setActive($record['active'] ==1)
                    ->setEstablishment($estabishmentRepository->findByName($record['establishment'])[0]);
                $entityManager->persist($user);
            }

            if ($correctData){
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateurs intégrés avec succès');
                return $this->redirectToRoute('outing_home');
            } else {
                $this->addFlash('warning', 'données incorrectes, veuillez vérifier votre fichier');
            }

        }


        return $this->render(
            'admin/csvusersadd.html.twig',
            [
                'csvFormView' => $csvForm->createView(),
            ]
        );
    }
}
