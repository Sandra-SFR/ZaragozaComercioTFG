<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaNewFormType;
use App\Form\ComercioCategoriaFormType;
use App\Form\ComercioNewFormType;
use DateTime;
use App\Entity\Comercio;
use App\Entity\Foto;
use App\Entity\Horario;
use App\Form\ComercioCreateForm;
use App\Form\CategoriaCreateFormType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            'comercios' => $comercios,
            'controller_name' => 'Comercios',
        ]);
    }

    #[Route('/categorias', name: 'admin_categorias', methods: ['GET'])]
    public function categorias(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        if (!in_array('ROLE_ADMIN', $rol)) {
            return $this->render('error/error.html.twig', [
                'codigo' => 403,
                'mensaje' => 'haha no tienes poder aquí',
                'imagen' => 'img/sirulogandalf.png',
            ]);
        }

        $categorias = $em->getRepository(Categoria::class)->findAll();
        $numero = [];
        foreach ($categorias as $categoria) {

            $comercios = $categoria->getComercios();
            $numero[$categoria->getNombre()] = count($comercios);
        }

        return $this->render('admin/categorias.html.twig', [
            'categorias' => $categorias,
            'numero' => $numero,
            'controller_name' => 'Categorias',
        ]);
    }

    #[Route('/comercio/new', name: 'comercio_new', methods: ['GET'])]
    public function new(EntityManagerInterface $em): Response
    {
        $comercio = new Comercio();
        $categorias = $em->getRepository(Categoria::class)->findAll();
        $form = $this->createForm(ComercioNewFormType::class, $comercio);

        return $this->render('admin/new.html.twig', [
            'comercio' => $comercio,
            'form' => $form,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/comercio/new', name: 'comercio_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $comercio = new Comercio();
        $comercio->setEstado(1); //Pone el estado en pendiente
        $categorias = $em->getRepository(Categoria::class)->findAll();
        $form = $this->createForm(ComercioNewFormType::class, $comercio);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user) {

            $comercio->setUsuario($user);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($comercio);
                $em->flush();

                //Crea la carpeta del comercio
                $fs = new Filesystem();
                $currentDir = __DIR__;
                $path = $currentDir . '/../../storage/' . $comercio->getId(); // Ruta completa al archivo

                if (!$fs->exists($path)) {
                    $fs->mkdir($path, 0755);
                }

                $file = $form['foto']->getData();// Guarda la foto del formulario

                if ($file instanceof UploadedFile) {
                    $currentDir = $this->getParameter('kernel.project_dir');
                    $path = $currentDir . '/storage/' . $comercio->getId() . '/';
                    $fs = new Filesystem();

                    $newFilename = uniqid() . '.' . $file->guessExtension(); //hash
                    $file->move($path, $newFilename);

                    // Crea una nueva entidad Foto y relaciona la foto con el comercio
                    $foto = new Foto();
                    $foto->setArchivo($newFilename);
                    $foto->setComercio($comercio);
                    $foto->setDestacada(true); // Para que la foto aparezca como foto principal

                    //Crea la carpeta thumb
                    if (!$fs->exists($path . "thumb")) {
                        $fs->mkdir($path . "thumb", 0775);
                    }
                    $this->fotoController->resizeImage($path, $newFilename, 1920, 1920, null);
                    $this->fotoController->resizeImage($path, $newFilename, 400, 400, 'thumb');

                    $em->persist($foto);
                    $em->flush();
                }

                return $this->redirectToRoute('admin_comercios', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('admin/new.html.twig', [
                'comercio' => $comercio,
                'categorias' => $categorias,
                'form' => $form,
            ]);
        } else {

            return $this->render('admin/new.html.twig', [
                'comercio' => $comercio,
                'categorias' => $categorias,
                'form' => $form,
            ]);
        }
    }

    #[Route('/categoria/new', name: 'categoria_new', methods: ['GET'])]
    public function newCategoria(EntityManagerInterface $em): Response
    {
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaNewFormType::class, $categoria);

        $user = $this->getUser();
        $rol = $user->getRoles();

        if (!in_array('ROLE_ADMIN', $rol)) {
            return $this->render('error/error.html.twig', [
                'codigo' => 403,
                'mensaje' => 'haha no tienes poder aquí',
                'imagen' => 'img/sirulogandalf.png',
            ]);
        }

        return $this->render('admin/newcat.html.twig', [
            'categoria' => $categoria,
            'form' => $form,
        ]);
    }

    #[Route('/categoria/new', name: 'categoria_create', methods: ['POST'])]
    public function createCategoria(Request $request, EntityManagerInterface $em): Response
    {
        $categoria = new Categoria();

        $form = $this->createForm(CategoriaNewFormType::class, $categoria);
        $form->handleRequest($request);

        $user = $this->getUser();
        $rol = $user->getRoles();

        if (!in_array('ROLE_ADMIN', $rol)) {
            return $this->render('error/error.html.twig', [
                'codigo' => 403,
                'mensaje' => 'haha no tienes poder aquí',
                'imagen' => 'img/sirulogandalf.png',
            ]);
        }

        if ($user) {

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($categoria);
                $em->flush();

                return $this->redirectToRoute('admin_categorias', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('admin/newcat.html.twig', [
                'categoria' => $categoria,
                'form' => $form,
            ]);
        } else {

            return $this->render('admin/newcat.html.twig', [
                'categoria' => $categoria,
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

        if ($usuario !== $comercio->getUsuario() && !in_array('ROLE_ADMIN', $rol)) {
            return $this->render('error/error.html.twig', [
                'codigo' => 403,
                'mensaje' => 'haha no tienes poder aquí',
                'imagen' => 'img/sirulogandalf.png',
            ]);
        }

        $categorias = $em->getRepository(Categoria::class)->findAll();
        $categoriaActual = $comercio->getCategorias();

        $ids = array_map(function($categoria) {
            return $categoria->getId();
        }, $categoriaActual->getValues());

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('comercio_edit', ['id' => $comercio->getId()]);
        }

        $horarios = $em->getRepository(Horario::class)->findHorarioComercio($comercio, ['dia' => 'ASC']);

        return $this->render('admin/comercio.html.twig', [
            'comercio' => $comercio,
            'form' => $form,
            'categorias' => $categorias,
            'fotos' => $comercio->getFotos(),
            'horas' => $horarios,
            'ids' => $ids,
        ]);
    }

    #[Route('/comercio/{id}/edit/cat', name: 'comercio_edit_cat', methods: ['POST'])]
    public function editCat(Request $request, Comercio $comercio, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        if ($usuario !== $comercio->getUsuario() && !in_array('ROLE_ADMIN', $rol)) {
            return $this->json(['code' => 403, 'message' => 'No tienes permisos para editar categorías.']);
        }

        $data = json_decode($request->getContent(), true);

        $borrarCat = $comercio->getCategorias()->getValues();

        foreach ($borrarCat as $categoria) {
            $comercio->removeCategoria($categoria);
        }

        $ids = $data['ids'] ?? null;

        $nuevaCategoria = $em->getRepository(Categoria::class)->find($ids);

        if ($nuevaCategoria) {
            $comercio->addCategoria($nuevaCategoria);
            $em->flush();
            return $this->json(['code' => 200]);
        }

        return $this->json(['code' => 200]);
    }

    #[Route('/categoria/{id}/edit', name: 'categoria_edit', methods: ['GET', 'POST'])]
    public function editCategoria(int $id, Request $request, Categoria $categoria, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoriaNewFormType::class, $categoria);
        $form->handleRequest($request);

        $usuario = $this->getUser();
        $rol = $usuario->getRoles();

        $categoria = $em->getRepository(Categoria::class)->find($id);

        if (!in_array('ROLE_ADMIN', $rol)) {
            return $this->render('error/error.html.twig', [
                'codigo' => 403,
                'mensaje' => 'haha no tienes poder aquí',
                'imagen' => 'img/sirulogandalf.png',
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_categorias', [
                'categoria' => $categoria], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categoria.html.twig', [
            'categoria' => $categoria,
            'form' => $form,
        ]);
    }

    #[Route('/comercio/{id}/delete', name: 'comercio_delete', methods: ['POST'])]
    public function delete(Comercio $comercio, EntityManagerInterface $entityManager): Response
    {
        //buscar la carpeta de fotos del comercio
        $fs = new Filesystem();
        $currentDir = __DIR__;
        $path = $currentDir . '/../../storage/' . $comercio->getId(); // Ruta completa al archivo

        $fs->remove($path); //Borra la carpeta de fotos del comercio

        $entityManager->remove($comercio);
        $entityManager->flush();

        $this->addFlash('success', 'El comercio ha sido eliminado con éxito.');

        return $this->redirectToRoute('admin_comercios');
    }

    #[Route('/categoria/{id}/delete', name: 'categoria_delete', methods: ['POST'])]
    public function deleteCategoria(Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($categoria);
        $entityManager->flush();

        $this->addFlash('success', 'La categoría ha sido eliminada con éxito.');

        return $this->redirectToRoute('admin_categorias');
    }

    #[Route('/foto/new', name: 'foto_add', methods: ['POST'])]
    public function addFoto(Request $request, EntityManagerInterface $entityManager): Response
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

    #[Route('/foto/{foto_id}/delete', name: 'foto_delete', methods: ['POST'])]
    public function deleteFoto(Foto $foto, EntityManagerInterface $em): Response
    {
        $comercioId = $foto->getComercio();
        $comercio = $em->getRepository(Comercio::class)->find($comercioId);


        $fs = new Filesystem();

        $currentDir = $this->getParameter('kernel.project_dir');
        $path = $currentDir . '/storage/' . $comercio->getId() . '/';

        $file = $foto->getArchivo();

        $fs->remove($path . "thumb/" . $file);
        $fs->remove($path . $file);

        $em->remove($foto);
        $em->flush();

        $this->addFlash('success', 'La foto ha sido eliminada con éxito.');

        return $this->redirectToRoute('comercio_edit', ['id' => $comercio->getId()]);
    }

    #[Route('/foto/{id}/destacar', name: 'foto_destacar', methods: ['GET', 'POST'])]
    public function destacarFoto(Request $request, Foto $foto, EntityManagerInterface $em): Response
    {
        $comercioId = $foto->getComercio();
        $comercio = $em->getRepository(Comercio::class)->find($comercioId);

        if ($request->isMethod('POST')) {
            // Desactivar todas las fotos destacadas del comercio
            foreach ($comercio->getFotos() as $f) {
                if ($f->isDestacada()) {
                    $f->setDestacada(false);
                }
            }

            // Activar la nueva foto destacada
            $foto->setDestacada(true);
            $em->flush();
        }
        return $this->json(['code' => 200]);
    }

    #[Route('/horario/new', name: 'horario_add', methods: ['POST'])]
    public function addHorario(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Obtener los valores del formulario
            $horaAperturaStr = $request->request->get('horaApertura');
            $horaCierreStr = $request->request->get('horaCierre');
            $dia = $request->request->get('dia');

            // Buscar el comercio por su ID
            $comercioId = $request->get('comercio');
            $comercio = $entityManager->getRepository(Comercio::class)->find($comercioId);

            $horaApertura = DateTime::createFromFormat('H:i', $horaAperturaStr);
            $horaCierre = DateTime::createFromFormat('H:i', $horaCierreStr);

            if ($comercio && $horaApertura && $horaCierre && $dia) {
                // Crear una nueva instancia de Horario
                $horario = new Horario();
                $horario->setHoraApertura($horaApertura);
                $horario->setHoraCierre($horaCierre);
                $horario->setComercio($comercio);
                $horario->setDia($dia);

                // Persistir y guardar en la base de datos
                $entityManager->persist($horario);
                $entityManager->flush();

                return $this->json(['code' => 200]);
            }
        }
        return $this->render('admin/comercio.html.twig');
    }

    #[Route('/horario/{horario_id}/delete', name: 'horario_delete', methods: ['POST'])]
    public function deleteHorario(Horario $horario, EntityManagerInterface $em): Response
    {
        $comercioId = $horario->getComercio();
        $comercio = $em->getRepository(Comercio::class)->find($comercioId);

        $em->remove($horario);
        $em->flush();

        $this->addFlash('success', 'El horario ha sido eliminada con éxito.');

        return $this->redirectToRoute('comercio_edit', ['id' => $comercio->getId()]);
    }

}
