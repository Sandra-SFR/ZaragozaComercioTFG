<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SwaggerUiController extends AbstractController
{
    #[Route('/apidocs', name: 'app_swagger_ui')]
    public function index(): Response
    {
        return $this->render('swagger_ui/index.html.twig');
    }
}
