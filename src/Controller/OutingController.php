<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingHomeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $homeOutingForm =$this->createForm(OutingHomeType::class);
        $homeOutingForm->handleRequest($request);
        $outings = null;

        if ($homeOutingForm->isSubmitted() && $homeOutingForm->isValid()){
            $outingRepository = $entityManager->getRepository(Outing::class);
            $data = $homeOutingForm->getData();



//            dump($data);
//            die();





            $outings = $outingRepository->findOutingForHome($this->getUser(), $data);


//            dump($outings);
//            die();



        }

        return $this->render('outing/home.html.twig', [
            'homeOutingFormView' => $homeOutingForm->createView(),
            'outings' =>$outings,
        ]);
    }
}
