<?php

namespace App\DataFixtures;

use App\Entity\TypeOfAbsence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TypeOfAbsenceFixtures  extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['AppFixtures'];
    }
    public function load(ObjectManager $manager): void
    {
        $typeofAbsencesArray = [
            ['name' => 'vacation'],
            ['name' => 'sick'],
            ['name' => 'unpaid'],
            ['name' => 'paid'],
            ['name' => 'other'],
        ];

        foreach($typeofAbsencesArray as $values){
            $typeOfAbsence = new TypeOfAbsence();
            foreach($values as $key => $value){
                $function = 'set'.ucfirst($key);
                $typeOfAbsence->$function($value);
            }
            $manager->persist($typeOfAbsence);
        }
        $manager->flush();
    }
}
