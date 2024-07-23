<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Configuration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    const TYPE_TRANSLATION = [
        'bool' => ['type'=> CheckboxType::class,'transformer'=>'App\Form\Transformers\BooleanToStringTransformer'],
        'int' => 'integer',
        'string' => 'text',
    ];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $configuration = $builder->getData();
        $transformer = self::TYPE_TRANSLATION[$configuration->getType()]['transformer'];
        $test = new $transformer();
        dump($test->transform(true));
        $builder->add('value', self::TYPE_TRANSLATION[$configuration->getType()]['type'], ['required' => false])
                ->add('submit', SubmitType::class);
        $builder->get('value')->addModelTransformer(new CallbackTransformer(
            function ($value): bool {
                // transform the array to a string
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
