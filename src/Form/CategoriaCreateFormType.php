<?php

namespace App\Form;

use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriaCreateFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [], // Las opciones se establecerán dinámicamente en el controlador
            'multiple' => false,
            'expanded' => true,
            'label' => 'Categorías',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
