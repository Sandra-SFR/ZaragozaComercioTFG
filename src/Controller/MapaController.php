<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapaController extends AbstractController
{
    #[Route('/mapa', name: 'app_mapa')]
    public function index(): Response
    {
        return $this->render('mapa/index.html.twig', [
            'controller_name' => 'MapaController',
        ]);
    }
}
