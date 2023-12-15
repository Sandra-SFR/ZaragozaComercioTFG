<?php

namespace App\Controller;

use App\Entity\Comercio;
use App\Entity\Horario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ComercioService;

#[Route('/comercio')]
class ComercioController extends AbstractController
{
    #[Route('/', name: 'app_comercios', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $comercios = $em->getRepository(Comercio::class)->findBy([],['nombre'=>'ASC']);

        return $this->render('comercio/index.html.twig', [
            'comercios'=>$comercios,
        ]);
    }

    #[Route('/{id}', name: 'app_comercio', methods: ['GET'])]
    public function comercio($id, EntityManagerInterface $em): Response
    {
        date_default_timezone_set('Europe/Madrid');

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

        return $this->render('comercio/comercio.html.twig', [
            'comercio'=>$comercio,
            'categoria' => $categoria,
            'horas' => $horarios,
            'estado' => $estadoComercio,
            'fotos' => $fotosJson,
        ]);
    }
}
