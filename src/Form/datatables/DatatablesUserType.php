<?php

namespace App\Form\datatables;

use App\Entity\User;
use App\Entity\WorkingGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatatablesUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('workingGroup', EntityType::class, [
                'class' => WorkingGroup::class,
                'choice_label' => 'name',
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
