<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Comercio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComercioCreateForm extends AbstractType
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('telefono', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('direccion', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('descripcion', TextareaType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'Pendiente' => 1,
                    'Abierto' => 2,
                    'Cerrado' => 3,
                    'Vacaciones' => 4,
                ],
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'estado-field'],
            ])
            ->add('categorias', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'multiple' => true,
                'expanded' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof Comercio) {
                $estado = $data->getEstado();
                $token = $this->tokenStorage->getToken();

                if ($token) {
                    $usuario = $token->getUser();

                    // Oculta el campo de estado si el estado es "Pendiente" y no es administrador
                    if (!$this->isAdmin($usuario) && $estado === 1) {
                        $form->remove('estado');
                    }

                    // Actualiza las opciones de 'choices' si el estado no es "Pendiente" y no es administrador
                    if (!$this->isAdmin($usuario) && $estado !== 1) {
                        $choices = [
                            'Abierto' => 2,
                            'Cerrado' => 3,
                            'Vacaciones' => 4,
                        ];

                        $form->add('estado', ChoiceType::class, [
                            'choices' => $choices,
                            'constraints' => [new NotBlank()],
                            'attr' => ['class' => 'estado-field'],
                        ]);
                    }
                }
            }
        });
    }

    private function isAdmin($usuario)
    {
        return in_array('ROLE_ADMIN', $usuario->getRoles());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comercio::class,
        ]);
    }
}