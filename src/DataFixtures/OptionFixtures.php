<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = [[
            'name'  => 'hasWekPlan',
            'value' => 1,
            'type'  => 'boolean',
        ]];
        foreach($values as $value){
            $option = new Configuration();
            $option->setName($value['name']);
            $option->setValue($value['value']);
            $option->setType($value['type']);
            $manager->persist($option);

        }
        
        $manager->flush();
    }
}
