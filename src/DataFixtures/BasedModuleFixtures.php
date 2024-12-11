<?php

namespace App\DataFixtures;

use App\Entity\ModuleConfiguration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BasedModuleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $moduleConfs = [   ['module'=>'task','type'=>'boolean','name'=>'isActive','description'=>'Task is active'], 
            ['module'=>'weekplan','type'=>'boolean','name'=>'isActive','description'=>'Weekplan is active'], 
            ['module'=>'project','type'=>'boolean','name'=>'isActive','description'=>'Project is active'], 
            ['module'=>'roomPlan','type'=>'boolean','name'=>'isActive','description'=>'Roomplan is active'], 
            ['module'=>'holiday','type'=>'boolean','name'=>'isActive','description'=>'Roomplan is active'], 
            ['module'=>'crm','type'=>'boolean','name'=>'isActive','description'=>'Roomplan is active'], 
            ['module'=>'timesheet','type'=>'boolean','name'=>'isActive','description'=>'Roomplan is active'], 
        ];  
        foreach ($moduleConfs as $moduleConf) {
            $module = new ModuleConfiguration();
            $module->setModule($moduleConf['module']);
            $module->setType($moduleConf['type']);
            $module->setName($moduleConf['name']);
            $module->setDescription($moduleConf['description']);
            $manager->persist($module);
        }


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['based'];
    }
}
