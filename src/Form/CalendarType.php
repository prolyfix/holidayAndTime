<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\TypeOfAbsence;
use App\Entity\User;
use App\Entity\WorkingGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',null,[
                    'widget'=>'single_text',
                    'attr'=>['class'=>'form-control','data-action'=>'change->hello#showHalfDay']
                    ]
            )
            ->add('endDate',null,['widget'=>'single_text','attr'=>['class'=>'form-control','data-action'=>'change->hello#showHalfDay']])
            ->add('startMorning',ChoiceType::class,[ 
                'choices'=>[
                    'halbesTag' => 1,
                    'vollesTag' => 0,
                ],
                'attr'=>['class'=>'form-control']
            ])
            ->add('endMorning',ChoiceType::class,[ 
                'choices'=>[
                    'halbesTag' => 1,
                    'vollesTag' => 0,
                ],
                'attr'=>['class'=>'form-control']
            ])
            ->add('workingGroup', EntityType::class, [
                'class' => WorkingGroup::class,
                'required' => false,
                'choice_label' => 'name',
                'attr'=>['class'=>'form-control']
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label' => 'email',
                'attr'=>['class'=>'form-control']
            ])
            ->add('typeOfAbsence', EntityType::class, [
                'class' => TypeOfAbsence::class,
                'choice_label' => 'name',
                'attr'=>['class'=>'form-control']
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
