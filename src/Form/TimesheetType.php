<?php

namespace App\Form;

use App\Entity\Commentable;
use App\Entity\Timesheet;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimesheetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime',null,['attr'=>['class'=>'form-control'],'widget'=>'single_text'])
            ->add('endTime',null,['attr'=>['class'=>'form-control'],'widget'=>'single_text'])
            ->add('break',null,['attr'=>['class'=>'form-control'],'widget'=>'single_text'])
            ->add('relatedCommentable',EntityType::class, [
                'class' => Commentable::class,
                'choice_label' => function($commentable){
                    if(method_exists($commentable, 'getName')) return $commentable->getName();
                    return $commentable->getId();
                },
                'attr'=>['class'=>'form-control']
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'attr'=>['class'=>'form-control']
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
