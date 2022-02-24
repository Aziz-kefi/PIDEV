<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoteamerController extends AbstractController
{
    /**
     * @Route("/coteamer", name="coteamer")
     */
    public function index(): Response
    {
        return $this->render('coteamer/index.html.twig', [
            'controller_name' => 'CoteamerController',
        ]);
    }
}
