<?php

namespace App\Form;

use App\Entity\Comercio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComercioCreateForm extends AbstractType
{
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
                    'Abierto' => 1,
                    'Cerrado' => 2,
                    'Vacaciones' => 3,
                ],
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comercio::class,
        ]);
    }
}