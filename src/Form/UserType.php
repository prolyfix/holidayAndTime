<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserWeekdayProperty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,['attr'=>['class'=>'form-control']])
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'attr'=>['class'=>'form-control']
            ])
            ->add('userWeekdayProperties', CollectionType::class, [
                'entry_type' => UserWeekdayPropertyType::class,
                
            ])
            ->add('userProperties', CollectionType::class, [
                'entry_type' => UserPropertyType::class,  
            ])
            ->add('userProperties', CollectionType::class, [
                'entry_type' => UserPropertyType::class,  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
