<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\TypeOfAbsence;
use App\Entity\User;
use App\Entity\WorkingGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('isAll')
            ->add('startDate')
            ->add('endDate')
            ->add('isAfternoon')
            ->add('workingGroup', EntityType::class, [
                'class' => WorkingGroup::class,
'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('typeOfAbsence', EntityType::class, [
                'class' => TypeOfAbsence::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
