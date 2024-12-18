<?php

namespace App\Form;

use App\Entity\TypeOfAbsence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeOfAbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,['attr'=>['class'=>'form-control']])
            ->add('isHoliday')
            ->add('isBankHoliday')
            ->add('hasToBeValidated')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeOfAbsence::class,
        ]);
    }
}
