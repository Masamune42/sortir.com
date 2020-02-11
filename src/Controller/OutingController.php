<?php

namespace App\Controller;

use App\Form\OutingHomeType;
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
    public function home(Request $request)
    {
        $homeOutingForm =$this->createForm(OutingHomeType::class);

        $homeOutingForm->handleRequest($request);

        return $this->render('outing/home.html.twig', [
            'homeOutingFormView' => $homeOutingForm->createView(),
        ]);
    }
}
