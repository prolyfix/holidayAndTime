<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Location;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const COMPANIES = [
        ['name'=>'company1',
        'location'=>[
            'street' => 'test street',
            'zipCode'=> '78333',
            'city'=> 'Noirmoutier'
        ],
        ]
    ];

    const USERS = [
        [   'name' => 'user1',
            'email'=>'user1@mittelstand.dev',
            'company' => 'company1'
        ],
        [   'name' => 'user2',
            'email'=> 'user2@mittelstand.dev',
            'company' => 'company1'        
        ]
    ];

    public static function getGroups(): array
    {
        return ['TestFixtures'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach($this::COMPANIES as $companyA){
            $company = new Company();
            $company->setName($companyA['name']);
            $locationE = new Location();
            $locationE->setStreet($companyA['location']['street']);
            $locationE->setCity($companyA['location']['city']);
            $locationE->setZipCode($companyA['location']['zipCode']);
            $company->setLocation($locationE);
            $manager->persist($company);
        }
        $manager->flush();
        foreach($this::USERS as $user){
            $company = $manager->getRepository(Company::class)->findOneBy(['name'=> $user['company']]);
            $userE = new User();
            $userE->setPassword("tralala");
            $userE->setName($user['name']);
            $userE->setEmail($user['email']);
            $userE->setCompany($company);
            $manager->persist($userE);
        }
        $manager->flush();
    }
}
