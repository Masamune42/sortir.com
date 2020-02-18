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
use Twig\Extra\Intl\IntlExtension;

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
     * @Route("/modify/{id}", name="modify", requirements={"id : \d+"})
     */
    public function create(Outing $outing = null, Request $request, EntityManagerInterface $entityManager)
    {

        if (!$outing) {
            $outing = new Outing();
            $outing->setOrganizer($this->getUser());

            $establishementUser = $this->getUser()->getEstablishment();
            $outing->setEstablishment($establishementUser);
        }

        $statutRepository = $entityManager->getRepository(Status::class);

        $outingForm = $this->createForm(OutingType::class, $outing);

        if ($request->request->has('date_start_firefox')) {

            $date_start = $request->request->get('date_start_firefox');
            $time_start = $request->request->get('time_start_firefox');
            $date_limit = $request->request->get('date_limit_firefox');
            $time_limit = $request->request->get('time_limit_firefox');

            $date_time_start = $date_start.'T'.$time_start;
            $date_time_limit = $date_limit.'T'.$time_limit;

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

                return $this->redirectToRoute('outing_home');
            } else {
                if ($outingForm->getClickedButton() && 'createOuting' === $outingForm->getClickedButton()->getName()) {
                    $statutCreated = $statutRepository->findOneBy(['nameTech' => 'published']);

                    $outing->setStatus($statutCreated);
                    $entityManager->persist($outing);
                    $entityManager->flush();

                    $this->addFlash('success', 'Sortie publiée.');

                    return $this->redirectToRoute('outing_home');
                }
            }

        }

        return $this->render(
            'outing/create.html.twig',
            ['outingFormView' => $outingForm->createView(),
                'outing' => $outing,
                'modif' => $outing->getId() !== null]
        );

    }

    /**
     * @Route("/cancel/{id}",name="cancel")
     */
    public function cancel(Outing $outing, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();

        if ($outing->getStatusDisplayAndActions($user)['cancelable']) {
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

        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas annuler cette sortie.');
        }


        return $this->redirectToRoute('outing_home');
    }

    /**
     * @Route("/publish/{id}",name="publish")
     */
    public function publish(Outing $outing, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();

        if ($outing->getStatusDisplayAndActions($user)['publishable']) {
            $statutRepository = $entityManager->getRepository(Status::class);

            $outing->setStatus($statutRepository->findOneBy(['nameTech' => 'published']));

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie publiée.');

        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas publier cette sortie.');
        }


        return $this->redirectToRoute('outing_home');
    }

    /**
     * @Route("/delete/{id}",name="delete")
     */
    public function delete(Outing $outing, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();

        if ($outing->getStatusDisplayAndActions($user)['deletable']) {

            $entityManager->remove($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie supprimée.');

        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas vous supprimer cette sortie.');
        }


        return $this->redirectToRoute('outing_home');
    }

    /**
     * @Route("/{id}",name="detail", requirements={"id : \d+"})
     */
    public function details($id, Outing $outing, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $statutRepository = $entityManager->getRepository(Status::class);

        $outingRepository = $entityManager->getRepository(Outing::class);
        $outing = $outingRepository->find($id);


        return $this->render(
            'outing/details.html.twig',
            compact('outing')
        );

    }
}

