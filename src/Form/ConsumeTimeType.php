<?php

namespace App\Form;

use App\Entity\Timesheet;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsumeTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('overtime',null,['attr'=>['class' => 'form-control'],'label' => 'Überstunden hinzufügen oder entfernen (in Minuten)'])
            ->add('overTimeAsTime',DateIntervalType ::class,[
                //'attr'=>['class' => 'form-control'],
                'label' => 'Überstunden hinzufügen oder entfernen',
                'mapped'   => false,
                'with_years' => false,
                'with_months' => false,
                'with_hours' => true,
            ])
            ->add('submit',SubmitType::class,['attr'=>['class' => 'btn btn-primary'],'label' => 'Speichern'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Timesheet::class,
        ]);
    }
}
