<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('endDate',DateType::class, [
                'label' => 'End Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('numberHolidayForYear',NumberType::class, [
                'label' => 'Number of holiday for the year',
            ])
            ->add('actualWorkingDays',NumberType::class, [
                'label' => 'Actual working days',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
