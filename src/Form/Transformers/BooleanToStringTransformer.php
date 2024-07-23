<?php
namespace App\Form\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class BooleanToStringTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transform the boolean value to a string representation
        if ($value === true) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function reverseTransform($value)
    {
        // Transform the string representation back to a boolean value
        if ($value === 'true') {
            return true;
        } else {
            return false;
        }
    }
}