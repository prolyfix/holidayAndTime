<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserWeekdayProperty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserWeekdayPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workingDay',ChoiceType::class,[
                'choices'=>[
                    'arbeitet nicht' => '0',
                    'volle Arbeitstag' => '1',  
                    'halbe Arbeitstag' => '0.5',
                ],
                'label'=>'workingDay',
                'attr'=>['class'=>'form-control']
            ])
            ->add('workingHours', TimeType::class, ['attr'=>['class'=>'form-control'],'widget'=>'single_text','required'=>false,'label'=>'workingHours'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserWeekdayProperty::class,
        ]);
    }
}
