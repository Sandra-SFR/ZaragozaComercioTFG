<?php

namespace App\Controller;

use App\Entity\Comercio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ComercioCreateForm;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/comercio', name: 'admin_comercios', methods: ['GET'])]
    public function comercios(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        $comercios = $em->getRepository(Comercio::class)->findNombresComercios($usuario, ['nombre' => 'ASC'], 20, 0);

        return $this->render('comercio/index.html.twig', [
            'comercios'=>$comercios,
        ]);
    }

    #[Route('/comercio/{id}', name: 'admin_comercio', methods: ['GET'])]
    public function comercio($id, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        $comercio = $em->getRepository(Comercio::class)->find($id);

        if ($usuario !== $comercio->getUsuario() && $rol !== 'ROLE_ADMIN') {
            return $this->render('error/error.html.twig', [
                'codigo'=>403,
                'mensaje'=>'haha no tienes poder aquí',
            ]);
        }

        return $this->render('comercio/comercio.html.twig', [
            'comercio'=>$comercio,
        ]);
    }

    #[Route('/comercio/new', name: 'comercio_new', methods: ['GET'])]
    public function new(): Response
    {
        $comercio = new Comercio();
        $form = $this->createForm(ComercioCreateForm::class, $comercio);

        return $this->render('comercio/new.html.twig', [
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

                return $this->redirectToRoute('admin_comercio', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('comercio/new.html.twig', [
                'comercio' => $comercio,
                'form' => $form,
            ]);
        } else {

            return $this->render('comercio/new.html.twig', [
                'comercio' => $comercio,
                'form' => $form,
            ]);
        }

    }
}
