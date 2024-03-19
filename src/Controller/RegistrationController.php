<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Security\AuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        return $this->registro($request, $userPasswordHasher, $userAuthenticator, $authenticator, $entityManager);
    }

    #[Route('/register/admin', name: 'app_register_admin')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        return $this->registro($request, $userPasswordHasher, $userAuthenticator, $authenticator, $entityManager);
    }

    private function registro(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $usuario = $this->getUser();
        $rol = $usuario ? $usuario->getRoles() : ['null'];

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
            // aquí se puede añadir que mande un email

            //comprueba si no es el administrador, y si no lo es lo loguea
            if (!in_array('ROLE_ADMIN', $rol) || !in_array('null', $rol)) {
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            } else {
                return $this->redirectToRoute('admin_usuarios');
            }
        }

        if (!in_array('ROLE_ADMIN', $rol) || !in_array('null', $rol)) {
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        } else {
            return $this->render('registration/admin.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }
    }
}
