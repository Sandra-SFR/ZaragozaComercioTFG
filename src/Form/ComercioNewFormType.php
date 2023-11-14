<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Comercio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComercioNewFormType extends AbstractType
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
            ->add('categorias', EntityType::class, [
                'class' => Categoria::class,
                'label' => 'Selecciona una categoría',
                'choice_label' => 'nombre',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('foto', FileType::class, [
                'label' => 'Añadir foto',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                    ]),
                ],
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
