<?php

namespace App\Form;

use App\Entity\Horario;
use Doctrine\ORM\Query\Expr\Select;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\WeekType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HorarioCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('horaApertura', TimeType::class)
            ->add('horaCierre', TimeType::class)
            ->add('dia', ChoiceType::class, [
                'choices' => [
                    'Lunes' => 1,
                    'Martes' => 2,
                    'Miercoles' => 3,
                    'Jueves' => 4,
                    'Viernes' => 5,
                    'Sabado' => 6,
                    'Domingo' => 7,
                ],
                'constraints' => [new NotBlank()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horario::class,
        ]);
    }
}
