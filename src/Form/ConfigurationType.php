<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Configuration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    const TYPE_TRANSLATION = [
        'bool' => ['type'=> CheckboxType::class,'transformer'=>'App\Form\Transformers\BooleanToStringTransformer'],
        'float' => ['type'=> NumberType::class],
        'int' => 'integer',
        'string' => 'text',
    ];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $configuration = $builder->getData();
        $builder->add('value', self::TYPE_TRANSLATION[$configuration->getType()]['type'], ['required' => false]);
        $builder->add('submit', SubmitType::class);
        if(isset(self::TYPE_TRANSLATION[$configuration->getType()]['transformer']))
            $builder->get('value')->addModelTransformer(new CallbackTransformer(
                function ($value): bool {
                    return $value == true;
                },
                function ($value): string {
                    // transform the string back to an array
                    return $value == 'true'?true:false;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
        ]);
    }
}
