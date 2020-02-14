<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\Outing;
use App\Entity\Status;
use App\Entity\User;
use App\Form\CSVType;
use App\Form\OutingHomeType;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/outing", name="outing_")
 */
class OutingController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(Request $request, EntityManagerInterface $entityManager)
    {
        $homeOutingForm = $this->createForm(OutingHomeType::class);
        $homeOutingForm->handleRequest($request);

        $outings = null;

        if ($homeOutingForm->isSubmitted() && $homeOutingForm->isValid()) {
            $outingRepository = $entityManager->getRepository(Outing::class);
            $data = $homeOutingForm->getData();


            $outings = $outingRepository->findOutingForHome($this->getUser(), $data);

        }
        return $this->render(
            'outing/home.html.twig',
            [
                'homeOutingFormView' => $homeOutingForm->createView(),
                'outings' => $outings,
            ]
        );
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $outing = new Outing();
        $outing->setOrganizer($this->getUser());

        $statutRepository = $entityManager->getRepository(Status::class);


        $establishementUser = $this->getUser()->getEstablishment();
        $outing->setEstablishment($establishementUser);


        $outingForm = $this->createForm(OutingType::class, $outing);

        if ($request->request->has('date_start_firefox')){

            $date_start= $request->request->get('date_start_firefox');
            $time_start= $request->request->get('time_start_firefox');
            $date_limit= $request->request->get('date_limit_firefox');
            $time_limit= $request->request->get('time_limit_firefox');

            $date_time_start=$date_start.'T'.$time_start;
            $date_time_limit=$date_limit.'T'.$time_limit;

            $outing_temp = $request->request->get('outing');
            $outing_temp['startTime'] = $date_time_start;
            $outing_temp['limitDateTime'] = $date_time_limit;

            dump($request);
            $request->request->remove('outing');
            $request->request->add(['outing' => $outing_temp]);
            dump($request);
        }

        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            if ($outingForm->getClickedButton() && 'save' === $outingForm->getClickedButton()->getName()) {
                $statutCreated = $statutRepository->findOneBy(['nameTech' => 'draft']);

                $outing->setStatus($statutCreated);
                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie sauvegardée.');

                return $this->redirectToRoute('home');
            } else {
                if ($outingForm->getClickedButton() && 'createOuting' === $outingForm->getClickedButton()->getName()) {
                    $statutCreated = $statutRepository->findOneBy(['nameTech' => 'published']);

                    $outing->setStatus($statutCreated);
                    $entityManager->persist($outing);
                    $entityManager->flush();

                    $this->addFlash('success', 'Sortie publiée.');

                    return $this->redirectToRoute('home');
                }
            }

        }

        return $this->render(
            'outing/create.html.twig',
            ['outingFormView' => $outingForm->createView()]
        );

    }

    /**
     * @Route("/delete/{id}",name="delete")
     */
    public function annuler(Outing $outing, EntityManagerInterface $entityManager, Request $request)
    {
        $statutRepository = $entityManager->getRepository(Status::class);
        $statutCanceled = $statutRepository->findOneBy(['nameTech' => 'canceled']);

        $motif = $request->request->get('motif');
        $outing->setStatus($statutCanceled);

        $currentInfos = $outing->getInfoOuting();
        $newInfos = "Sortie annulée pour la raison suivante : \n".$motif."\nAncienne description :\n".$currentInfos;
        $outing->setInfoOuting($newInfos);

        $entityManager->persist($outing);
        $entityManager->flush();

        $this->addFlash('success', 'Sortie annulée.');

        return $this->redirectToRoute('outing_home');
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
            'outing/csvusersadd.html.twig',
            [
                'csvFormView' => $csvForm->createView(),
            ]
        );
    }
}

