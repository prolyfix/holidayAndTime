<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserProperty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('holidayPerYear',null, ['attr'=>['class'=>'form-control'],'label'=>'holidayPerYear'])
            ->add('year',null, ['attr'=>['class'=>'form-control'],'label'=>'year'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProperty::class,
        ]);
    }
}
