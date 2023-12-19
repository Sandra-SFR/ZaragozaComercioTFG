<?php

namespace App\Controller;

use App\Entity\Comercio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/storage')]
class StorageController extends AbstractController
{
    #[Route('/', name: 'app_storage')]
    public function index(): Response
    {

        return $this->render('storage/index.html.twig', [
            'controller_name' => 'StorageController',
        ]);
    }

    #[Route("/thumb/{comercio_id}/{filename}", name: "storage_file_thumb")]
    public function getFileThumb($comercio_id ,string $filename)
    {
        // Obtener la ruta actual del archivo
        //TODO: quitar todo
        $currentDir = __DIR__;
        // La ruta real al archivo en la carpeta "storage"
//        $filePath = $this->getParameter('kernel.project_dir') . '/storage/' . $filename;
        $filePath = $currentDir.'/../../storage/' . $comercio_id . '/thumb/' . $filename; // Ruta completa al archivo

        // Verificar que el archivo exista
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('El archivo no existe.');
        }

        // Crear una BinaryFileResponse para enviar el archivo
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename // Puedes personalizar el nombre del archivo aquí
        );

        return $response;
    }

    #[Route("/{comercio_id}/{filename}", name: "storage_file")]
    public function getFile($comercio_id ,string $filename)
    {
        // Obtener la ruta actual del archivo
        $currentDir = __DIR__;
        // La ruta real al archivo en la carpeta "storage"
//        $filePath = $this->getParameter('kernel.project_dir') . '/storage/' . $filename;
        $filePath = $currentDir.'/../../storage/' . $comercio_id . '/' . $filename; // Ruta completa al archivo

        // Verificar que el archivo exista
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('El archivo no existe.');
        }

        // Crear una BinaryFileResponse para enviar el archivo
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename // Puedes personalizar el nombre del archivo aquí
        );

        return $response;
    }


}
