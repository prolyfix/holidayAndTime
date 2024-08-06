<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserSchedule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('effectiveDate', null, [
                'widget' => 'single_text',
            ])
            ->add('UserWeekdayProperties', CollectionType::class, [
                'entry_type' => UserWeekdayPropertyType::class,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSchedule::class,
        ]);
    }
}
