<?php

namespace App\Controller;

use App\Entity\Comercio;
use App\Form\ComercioCreateForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin')]
class AdminController extends AbstractController
{
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

            return $this->redirectToRoute('admin_comercios', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('admin/comercio.html.twig', [
            'comercio' => $comercio,
            'form' => $form,
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
