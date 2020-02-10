<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OutingController extends AbstractController
{
    /**
     * @Route("/outing", name="outing")
     */
    public function index()
    {
        return $this->render('outing/index.html.twig', [
            'controller_name' => 'OutingController',
        ]);
    }
}
