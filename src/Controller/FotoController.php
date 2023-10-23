<?php

namespace App\Controller;

use App\Entity\Comercio;
use App\Entity\Foto;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foto')]
class FotoController extends AbstractController
{
    #[Route('/', name: 'foto_index')]
    public function index(): Response
    {
        return $this->render('foto/index.html.twig', [
            'controller_name' => 'FotoController',
        ]);
    }

    public function resizeImage($path,$file,$width,$height, $sub = null) {
        $img = new \Imagick();
        $img->readImage($path.$file);
        if ($sub != null) {
            $path = $path.$sub."/";
        }
        $img->resizeImage($width,$height,\Imagick::FILTER_LANCZOS,1,TRUE);
        $img->setImageFormat('jpeg');
        $img->setImageCompressionQuality(60);
        $img->writeImage($path.$file);
        $img->clear();
        $img->destroy();
    }

    public function new(Request $request, EntityManagerInterface $em): Response
    {
//        $helpers = $this->get("app.helpers");

        $id = $request->get("id", null);

//        $title = $request->get("title", null);

        $hash = $request->get("authorization", null);

        $check = $helpers->authCheck($hash);

        if ($check) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->find($identity->sub);

            $comercio = $em->getRepository("BackendBundle:Comercio")->find($id);

            $register = $em->getRepository("BackendBundle:Register")->findOneBy(array(
                "user" => $identity->sub,
                "comercio" => $id
            ));

            $files = $request->files->get("image");

            foreach ($files as $file) {

                if (!empty($file) && $file != null && is_object($register)) {

                    $type = $file->getMimeType();

                    $privilege = $register->getPrivilege();
                    if ($privilege < 3) {
                        $f = "uploads/events/event_".$comercio->getId();
                        $size = 0;
                        if($f!==false && $f!='' && file_exists($f)){
                            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($f, FilesystemIterator::SKIP_DOTS)) as $object){
                                $size += $object->getSize();
                            }
                        }
                        if ($comercio->getStorage() > $size/(1024*1024)) {

                            if (strpos($type, "image/") !== false) {
                                $file_name = uniqid() . ".jpg";
                                $path = "uploads/events/event_" . $id . "/";
                                $file->move($path, $file_name);
//                                $zipName = $comercio->getCode() . '.zip';

                                $fs = new Filesystem();

                                if (!$fs->exists($path . "large")) {
                                    $fs->mkdir($path . "large", 0775);
                                }
                                if (!$fs->exists($path . "medium")) {
                                    $fs->mkdir($path . "medium", 0775);
                                }
                                if (!$fs->exists($path . "thumb")) {
                                    $fs->mkdir($path . "thumb", 0775);
                                }
//                                if ($fs->exists($path . $zipName)) {
//                                    $fs->remove($path . $zipName);
//                                }

                                $helpers->resizeImage($path, $file_name, 2000, 2000, "large");
                                $helpers->resizeImage($path, $file_name, 600, 600, "medium");
                                $helpers->resizeImage($path, $file_name, 300, 300, "thumb");

                                $fs->remove($path . $file_name);

//                                $zip = new ZipArchive();
//                                if ($zip->open($path . $zipName, ZipArchive::CREATE)) {
//                                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path . "large"), RecursiveIteratorIterator::LEAVES_ONLY);
//                                    foreach ($files as $name => $filename) {
//                                        if (!is_dir($filename)) {
//                                            $new_filename = substr($name, strrpos($name, '/') + 1);
//                                            $zip->addFile($filename, $new_filename);
//                                        }
//                                    }
//
//                                    $ok = $zip->close();
//                                } else {
//                                    echo "maaaal";
//                                }
//
//                                $createdAt = new \Datetime('now');

                                $photo = new Foto();
                                $photo->setArchivo($file_name);
//                                $photo->setImagePath($path);
                                $photo->setComercio($comercio);
//                                $photo->setTitle($title);
//                                $photo->setUser($user);
//                                $photo->setCreatedAt($createdAt);

                                $em->persist($photo);
                                $em->flush();

                                $data = array(
                                    "status" => "success",
                                    "code" => 200,
                                    "msg" => "Image uploaded"
                                );
                            } else {
                                $data = array(
                                    "status" => "error",
                                    "code" => 400,
                                    "msg" => "File is not image"
                                );
                            }
                        } else {
                            $data = array(
                                "status" => "error",
                                "code" => 400,
                                "msg" => "Max storage overload"
                            );
                        }
                    } else {
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "msg" => "User not valid"
                        );
                    }
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "Imagen not uploaded"
                    );
                }
            }

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Authorization not valid"
            );
        }

        return $helpers->toJson($data);
    }

    public function edit(int $id, Request $request, Comercio $comercio, EntityManagerInterface $em): Response
    {
        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);

        $check = $helpers->authCheck($hash);

        $json = $request->get("json", null);

        if ($check) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(array(
                "id" => $identity->sub
            ));

            $photo = $em->getRepository("BackendBundle:Photo")->findOneBy(array(
                "id" => $id
            ));

            $event = $photo->getEvent();

            $register = $em->getRepository("BackendBundle:Register")->findOneBy(array(
                "user" => $user,
                "event" => $event
            ));

            if ($json != null && is_object($user) && is_object($photo) && is_object($event) && is_object($register)) {

                $params = json_decode($json);

                $privilege = $register->getPrivilege();
                if ($privilege < 2 || $photo->getUser() == $user || $user->getRole() == 0) {

                    $updatedAt = new \Datetime('now');
                    $title = $params->title;
                    $public = $params->public;

                    $photo->setTitle($title);
                    $photo->setPublic($public);
                    $photo->setUpdatedAt($updatedAt);

                    $em->persist($photo);
                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Image updated"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "User not valid"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Imagen not updated, params not valid"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Authorization not valid"
            );
        }

        return $helpers->toJson($data);
    }

    public function remove(Request $request, $id = null): Response
    {
        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);

        $check = $helpers->authCheck($hash);

        if ($check) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(array(
                "id" => $identity->sub
            ));

            $photo = $em->getRepository("BackendBundle:Photo")->findOneBy(array(
                "id" => $id
            ));

            $event = $photo->getEvent();

            $register = $em->getRepository("BackendBundle:Register")->findOneBy(array(
                "user" => $user,
                "event" => $event
            ));

            $comments = $em->getRepository("BackendBundle:Comment")->findBy(array(
                "photo" => $photo->getId()
            ));

            if (is_object($user) && is_object($photo) && is_object($event) && is_object($register)) {

                $privilege = $register->getPrivilege();
                if ($privilege < 2 || $photo->getUser() == $user || $user->getRole() == 0) {

                    $fs = new Filesystem();

                    $path = $photo->getImagePath();
                    $file_name = $photo->getImage();

                    $fs->remove($path . "large/" . $file_name);
                    $fs->remove($path . "medium/" . $file_name);
                    $fs->remove($path . "thumb/" . $file_name);
                    $zipName = $event->getCode() . '.zip';
                    if ($fs->exists($path . $zipName)) {
                        $fs->remove($path . $zipName);
                    }

//                    $zip = new ZipArchive();
//                    if ($zip->open($path . $zipName, ZipArchive::CREATE)) {
//                        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path . "large"), RecursiveIteratorIterator::LEAVES_ONLY);
//                        foreach ($files as $name => $filename) {
//                            if (!is_dir($filename)) {
//                                $new_filename = substr($name, strrpos($name, '/') + 1);
//                                $zip->addFile($filename, $new_filename);
//                            }
//                        }
//
//                        $ok = $zip->close();
//                    } else {
//                        echo "maaaal";
//                    }

                    if (is_object($comments)) {
                        $em->remove($comments);
                        $em->flush();
                    }

                    $em->remove($photo);

                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Image removed"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "User not valid"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Imagen not updated, params not valid"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Authorization not valid"
            );
        }

        return $helpers->toJson($data);
    }

    public function show(Request $request, $id = null): Response
    {
        $helpers = $this->get("app.helpers");

        $hash = $request->headers->get("authorization", null);

        $check = $helpers->authCheck($hash);

        if ($check) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(array(
                "id" => $identity->sub
            ));

            $photo = $em->getRepository("BackendBundle:Photo")->findOneBy(array(
                "id" => $id
            ));

            $event = $photo->getEvent();

            $register = $em->getRepository("BackendBundle:Register")->findOneBy(array(
                "user" => $user,
                "event" => $event
            ));

            $comments = $em->getRepository("BackendBundle:Comment")->findBy(array(
                "photo" => $photo
            ), array(
                "createdAt" => "DESC"
            ));

            if (is_object($user) && is_object($photo) && is_object($event) && is_object($register)) {

                $privilege = $register->getPrivilege();
                if ($privilege < 3 || $photo->getUser() == $user || $user->getRole() == 0) {

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $photo,
                        "privilege" => $privilege,
                        "comments" => $comments
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "User not valid"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Imagen not updated, params not valid"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Authorization not valid"
            );
        }

        return $helpers->toJson($data);
    }
}
