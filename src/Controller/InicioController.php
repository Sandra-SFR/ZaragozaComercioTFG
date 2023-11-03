<?php

namespace App\Controller;

use App\Entity\Comercio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    #[Route('/', name: 'app_inicio')]
    public function index(): Response
    {

//        //TODO: Pagina de inicio con buscardor, Carga del buscador con AJAX
        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
        ]);
    }

    #[Route('/buscar', name: 'buscar')]
    public function bucar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $searchTerm = $request->query->get('search');

        // Realiza la búsqueda en la base de datos
        $comercios = $em->getRepository(Comercio::class)->buscadorComercios($searchTerm);


        // Convierte los resultados en un formato JSON
        $result = [];
        foreach ($comercios as $comercio) {
            $result[] = [
                'id' => $comercio->getId(),
                'nombre' => $comercio->getNombre(),
                'descripcion' => $comercio->getDescripcion(),
            ];
        }

        return new JsonResponse($result);
    }
}
