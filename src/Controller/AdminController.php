<?php

namespace App\Controller;

use App\Controller\FotoController;
use App\Entity\Comercio;
use App\Entity\Foto;
use App\Form\ComercioCreateForm;
use App\Form\FotoCreateForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $fotoController;

    public function __construct(FotoController $fotoController)
    {
        $this->fotoController = $fotoController;
    }
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/comercios', name: 'admin_comercios', methods: ['GET'])]
    public function comercios(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        $comercios = $em->getRepository(Comercio::class)->findNombresComercios($usuario, ['nombre' => 'ASC'], 20, 0);

        return $this->render('admin/comercios.html.twig', [
            'comercios'=>$comercios,
            'controller_name' => 'Comercios',
        ]);
    }

    #[Route('/comercio/new', name: 'comercio_new', methods: ['GET'])]
    public function new(): Response
    {
        $comercio = new Comercio();
        $form = $this->createForm(ComercioCreateForm::class, $comercio);

        return $this->render('admin/new.html.twig', [
            'comercio' => $comercio,
            'form' => $form,
        ]);
    }

    #[Route('/comercio/new', name: 'comercio_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $comercio = new Comercio();
        $form = $this->createForm(ComercioCreateForm::class, $comercio);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user) {

            $comercio->setUsuario($user);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($comercio);
                $em->flush();

                $fs = new Filesystem();
                $currentDir = __DIR__;
                $path = $currentDir.'/../../storage/' . $comercio->getId() ; // Ruta completa al archivo

                if (!$fs->exists($path)) {
                    $fs->mkdir($path, 0755);
                }

                return $this->redirectToRoute('admin_comercios', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('admin/new.html.twig', [
                'comercio' => $comercio,
                'form' => $form,
            ]);
        } else {

            return $this->render('admin/new.html.twig', [
                'comercio' => $comercio,
                'form' => $form,
            ]);
        }

    }

    #[Route('/comercio/{id}/edit', name: 'comercio_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, Comercio $comercio, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ComercioCreateForm::class, $comercio);
        $form->handleRequest($request);

        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        $comercio = $em->getRepository(Comercio::class)->find($id);

        if ($usuario !== $comercio->getUsuario() && $rol != 'ROLE_ADMIN') {
            return $this->render('error/error.html.twig', [
                'codigo'=>403,
                'mensaje'=>'haha no tienes poder aquí',
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_comercios', [
                'comercio' => $comercio], Response::HTTP_SEE_OTHER);
        }


        return $this->render('admin/comercio.html.twig', [
            'comercio' => $comercio,
            'form' => $form,
            'fotos' =>$comercio->getFotos(),
        ]);
    }

    #[Route('/comercio/{id}/delete', name: 'comercio_delete', methods: ['POST'])]
    public function delete(Comercio $comercio, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comercio);
        $entityManager->flush();

        $this->addFlash('success', 'El comercio ha sido eliminado con éxito.');

        return $this->redirectToRoute('admin_comercios');
    }

    #[Route('/foto/new', name: 'foto_add', methods: ['POST'])]
    public function addFoto(Request $request,EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('foto');
            $comercioId = $request->get('comercio');
            $comercio = $entityManager->getRepository(Comercio::class)->find($comercioId);

            if ($file) {
                $currentDir = $this->getParameter('kernel.project_dir');
                $path = $currentDir . '/storage/' . $comercio->getId() . '/';
                $newFilename = uniqid() . '.' . $file->guessExtension(); //hash

                // Mueve el archivo al directorio de destino
                $file->move($path, $newFilename);

                $foto = new Foto();
                $foto->setArchivo($newFilename);
                $foto->setComercio($comercio);
                $foto->setDestacada(false);

                $fs = new Filesystem();
                if (!$fs->exists($path . "thumb")) {
                    $fs->mkdir($path . "thumb", 0775);
                }
                $this->fotoController->resizeImage($path, $newFilename, 1920, 1920, null);
                $this->fotoController->resizeImage($path, $newFilename, 400, 400, 'thumb');
            }
                $entityManager->persist($foto);
                $entityManager->flush();

                return $this->redirectToRoute('comercio_edit', ['id' => $comercio->getId()]);
            }

        return $this->render('admin/comercio.html.twig');
    }

//    #[Route('/foto/new', name: 'foto_add', methods: ['GET', 'POST'])]
//    public function newFoto(Request $request, Comercio $comercio): Response
//    {
//        $foto = new Foto();
//        $formFoto = $this->createForm(FotoCreateForm::class, $foto);
//
//        $formFoto->handleRequest($request);
//
//        if ($formFoto->isSubmitted() && $formFoto->isValid()) {
//            // Manejar la carga de la foto
//            $file = $formFoto['archivo']->getData();
//            if ($file) {
//                $currentDir = __DIR__;
//                $path = $currentDir . '/../../storage/' . $comercio->getId();
//                $newFilename = uniqid() . '.' . $file->guessExtension(); //hash
//
//                // Mueve el archivo al directorio de destino
//                $file->move($path, $newFilename);
//
//                $foto->setArchivo($newFilename);
//                $foto->setComercio($comercio);
//                $foto->setDestacada(false); // Opcional: establecer destacada en false por defecto
//                $this->fotoController->resizeImage($path,$file,352,235,null);
//            }
//
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($foto);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('comercio_edit', ['id' => $comercio->getId()]);
//        }
//
//        return $this->render('admin/comercio.html.twig', [
//            'form_foto' => $formFoto->createView(),
//        ]);
//    }


//    #[Route('/comercio/{id}', name: 'comercio_delete', methods: ['POST'])]
//    public function delete(Request $request, Comercio $comercio, EntityManagerInterface $em): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$comercio->getId(), $request->request->get('_token'))) {
//            $em->remove($comercio);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('admin_comercios', [], Response::HTTP_SEE_OTHER);
//    }

//    #[Route('/comercio/{id}', name: 'admin_comercio', methods: ['GET'])]
//    public function comercio(int $id, EntityManagerInterface $em): Response
//    {
//        $usuario = $this->getUser();
//        $rol = $usuario->getRoles();
//
//        $comercio = $em->getRepository(Comercio::class)->find($id);
//
//        if ($usuario !== $comercio->getUsuario() && $rol != 'ROLE_ADMIN') {
//            return $this->render('error/error.html.twig', [
//                'codigo'=>403,
//                'mensaje'=>'haha no tienes poder aquí',
//            ]);
//        }
//
//        return $this->render('admin/comercio.html.twig', [
//            'comercio'=>$comercio,
//        ]);
//    }


}
