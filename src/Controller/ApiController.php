<?php

namespace App\Controller;

use App\Entity\Comercio;
use App\Entity\Horario;
use App\Service\ComercioService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Endpoints API
 * estos endpoints no tienen interfaz y solo devuelven datos en Json
 **/
#[Route('/api')]
class ApiController extends AbstractController
{
//    Vista publica de comercios

    /**
     * Endpoint API que muestra todos los comercios
     * no requiere auth (JWT)
    **/
    #[Route('/comercios', name: 'app_api_comercios', methods: ['GET'])]
    public function index(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $comercios = $em->getRepository(Comercio::class)->findBy([],['nombre'=>'ASC']);
        $comerciosData = [];

        foreach ($comercios as $comercio) {
            $categorias = $comercio->getCategorias()->getValues();
            $categoria = $categorias[0]->getNombre();

            $comercioData = [
                'id' => $comercio->getId(),
                'nombre' => $comercio->getNombre(),
                'telefono' => $comercio->getTelefono(),
                'direccion' => $comercio->getDireccion(),
                'email' => $comercio->getEmail(),
                'estado' => $comercio->getEstado(),
                'categoria' => $categoria,
                'descripcion' => $comercio->getDescripcion(),
            ];

            // Agregamos los datos del comercio al arreglo de todos los comercios
            $comerciosData[] = $comercioData;
        }

        $data = $serializer->serialize([
            'comercios' => $comerciosData,

        ], 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * Endpoint API que muestra un comercio por {id}
     * param $id
     * no requiere auth (JWT)
     **/
    #[Route('/comercio/{id}', name: 'app_api_comercio', methods: ['GET'])]
    public function comercio($id, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        date_default_timezone_set('Europe/Madrid');// es necesario para mostrar la hora correcta

        $comercio = $em->getRepository(Comercio::class)->find($id);

        $categorias = $comercio->getCategorias()->getValues();
        $categoria = $categorias[0]->getNombre();

        $horarios = $em->getRepository(Horario::class)->findHorarioComercio($comercio, ['dia' => 'ASC']);

        $comercioService = new ComercioService();
        $horas = $horarios;
        $estadoComercio = $comercioService->verificarEstadoComercio($horas);

        $fotos = $comercio->getFotos()->getValues();
        $archivos = array_map(function ($foto){
            return $foto->getArchivo();
        }, $fotos);
        $fotosJson = json_encode($archivos);

        $data =$serializer->serialize([
        'comercio'=>[
            'id' =>$comercio->getId(),
            'nombre' =>$comercio->getNombre(),
            'telefono' => $comercio->getTelefono(),
            'direccion' => $comercio->getDireccion(),
            'email' => $comercio->getEmail(),
            'estado' => $comercio->getEstado(),
            'descripcion' => $comercio->getDescripcion(),
            'descripcion larga' => $comercio->getDescripcionLarga(),
        ],
            'categoria' => $categoria,
            'horas' => $horarios,
            'estado' => $estadoComercio,
            'fotos' => $fotosJson,
        ],'json', ['groups' => 'comercio'] );

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * Endpoints API
     * requieren auth (JWT)
     **/

    // Login y logout

}
