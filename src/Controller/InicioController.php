<?php

namespace App\Controller;

use App\Entity\Categoria;
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
    public function index(EntityManagerInterface $em): Response
    {
        $categorias = $em->getRepository(Categoria::class)->findAll();

        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
            'categorias' => $categorias,
        ]);
    }

    #[Route('/buscar', name: 'buscar')]
    public function buscar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $searchTerm = $request->query->get('search');

        // Realiza la búsqueda en la base de datos
        $comercios = $em->getRepository(Comercio::class)->buscadorComercios($searchTerm);


        // Convierte los resultados en un formato JSON
        $result = [];
        foreach ($comercios as $comercio) {
            $result[] = [
                'id' => $comercio['id'],
                'nombre' => $comercio['nombre'],
                'descripcion' => $comercio['descripcion'],
                'foto' => $comercio['archivo'],
                'categoria' => $comercio['categoria'],
                'estado' => $comercio['estado'],
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/categoria/{id}', name: 'app_categoria')]
    public function categoria($id,EntityManagerInterface $em): Response
    {
        $categoria = $em->getRepository(Categoria::class)->find($id);

        $comercios = $categoria->getComercios();

        return $this->render('inicio/categoria.html.twig', [
            'controller_name' => 'InicioController',
            'comercios' => $comercios,
            'categoria' => $categoria,
        ]);
    }

}
