<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\Outing;
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
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): \Symfony\Component\HttpFoundation\Response {

        //create new user with form 
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Compte Utilisateur créé');
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

        return $this->render(
            'admin/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/csvadd", name="csvadd")
     */
    public function csvadd(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $csvForm = $this->createForm(CSVType::class);

        $csvForm->handleRequest($request);

        if ($csvForm->isSubmitted() && $csvForm->isValid()) {

            $file = $csvForm['csv_file']->getData()->openFile();

            $reader = Reader::createFromFileObject($file)
                ->setHeaderOffset(0);

            $estabishmentRepository = $entityManager->getRepository(Establishment::class);


            $userRepository = $entityManager->getRepository(User::class);

            $corruptedLine = false;
            $doubleUser = false;
            $nbNewUsers = 0;
            foreach ($reader as $record) {
                if (isset($record['establishment'])){
                    $establishment = $estabishmentRepository->findByName($record['establishment']);
                    if (count($establishment)>0){
                        $record['establishment'] = $establishment[0];
                    } else {
                        unset($record['establishment']); //if the establishment is not found, unset establishment
                    }
                }

                if (!isset($record['password'])
                    || !isset($record['username'])
                    || !isset($record['firstname'])
                    || !isset($record['name'])
                    || !isset($record['phone'])
                    || !isset($record['mail'])
                    || !isset($record['administrator'])
                    || !isset($record['active'])
                    || !isset($record['establishment'])
                ) {
                    $corruptedLine = true;

                } elseif (count(//check if there is already a user with the same user name or the same email
                        $userRepository->findByUsernameOrMail($record['username'],$record['mail']
                        )
                    ) != 0) {
                    $doubleUser = true;
                } else {
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
                        ->setActive($record['active'] == 1)
                        ->setEstablishment($record['establishment']);
                    $entityManager->persist($user);
                    $nbNewUsers++;
                }

            }

            if ($doubleUser) {
                $this->addFlash(
                    'warning',
                    'Un ou plusieurs utilisateurs ont un nom d\'utilisateur ou un email déjà utilisé(s), veuillez vérifier votre fichier'
                );
            }

            if ($corruptedLine) {
                $this->addFlash(
                    'warning',
                    'Une ou plusieurs lignes ne sont pas au bon format ou ont des informations manquantes, veuillez vérifier votre fichier'
                );
            }

            if ($nbNewUsers > 0){
                $entityManager->flush();

                $this->addFlash('success', $nbNewUsers.' nouveau(x) utilisateur(s) ajouté(s).');
            }


            return $this->redirectToRoute('admin_csvadd');
        }

        return $this->render(
            'admin/csvusersadd.html.twig',
            [
                'csvFormView' => $csvForm->createView(),
            ]
        );
    }

    /**
     * @Route("/deactivate/{id}",name="deactivate", requirements={"id : \d+"})
     */
    public function deactivate(User $userToDeactivate, EntityManagerInterface $entityManager)
    {
        //deactivate the user
        $userToDeactivate->setActive(false);
        $entityManager->persist($userToDeactivate);
        $entityManager->flush();

        //unregister to all not started outings that the user is registered to
        $this->unregister($userToDeactivate, $entityManager);


        $this->addFlash('success', 'Utilisateur '.$userToDeactivate->getUsername().' désactivé.');

        return $this->redirectToRoute('admin_users_list');

    }

    /**
     * @Route("/reactivate/{id}",name="reactivate", requirements={"id : \d+"})
     */
    public function reactivate(User $userToDeactivate, EntityManagerInterface $entityManager)
    {
        //reactivate the user
        $userToDeactivate->setActive(true);
        $entityManager->persist($userToDeactivate);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateurs '.$userToDeactivate->getUsername().' réactivé.');

        return $this->redirectToRoute('admin_users_list');

    }

    /**
     * @Route("/delete/{id}",name="delete", requirements={"id : \d+"})
     */
    public function delete(User $userToDelete, EntityManagerInterface $entityManager)
    {

        //unregister to all not started outings that the user is registered to
        $this->unregister($userToDelete, $entityManager);

        //change the organizer to all outings organized bu this user if there are participant, delete it otherwise
        $outingsToReorganize = $entityManager->getRepository(Outing::class)->findBy(['organizer' => $userToDelete]);
        $ghostOrganizer = $entityManager->getRepository(User::class)->findBy(['username' => 'utilisateur_supprimé'])[0];

        foreach ($outingsToReorganize as $outing) {
            if (count($outing->getParticipant()) == 0) {
                $entityManager->remove($outing);
            } else {
                $outing->setOrganizer($ghostOrganizer);
                $entityManager->persist($outing);
            }
        }

        //delete user
        $entityManager->remove($userToDelete);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateurs '.$userToDelete->getUsername().' désactivé.');

        return $this->redirectToRoute('admin_users_list');

    }

    private function unregister($user, EntityManagerInterface $entityManager)
    {
        //unregister to all not started outings that the user is registered to
        $outingsToUnregister = [];
        $outingRepository = $entityManager->getRepository(Outing::class);
        foreach ($entityManager->getRepository(Establishment::class)->findAll() as $establishment) {
            $outingsToUnregister = array_merge(
                $outingsToUnregister,
                $outingRepository->findOutingForHome(
                    $user,
                    [
                        'establishment' => $establishment,
                        'nameContent' => null,
                        'dateMin' => null,
                        'dateMax' => null,
                        'iAmOrganizer' => false,
                        'passedOuting' => false,
                        'iAmNotRegistred' => false,
                        'iAmRegistred' => true,
                    ]
                )
            );
        }
        foreach ($outingsToUnregister as $outingToUnregister) {
            $outingToUnregister->removeParticipant($user);
            $entityManager->persist($outingToUnregister);
        }

        $entityManager->flush();
    }

    /**
     * @Route("/userslist", name="users_list")
     */
    public function usersList(EntityManagerInterface $entityManager)
    {
        $usersList = $entityManager->getRepository(User::class)->findAll();

        return $this->render(
            'admin/userslist.html.twig',
            [
                'usersList' => $usersList,
            ]
        );

    }

}
