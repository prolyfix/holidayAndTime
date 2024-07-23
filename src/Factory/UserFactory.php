<?php
namespace App\Factory;

use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\Location;
use App\Entity\User;

class UserFactory
{
    const COMPANY_CONFIGURATIONS = ['hasTask'=>'bool','hasWeekplan'=>'bool'];
    public static function createCustomer(): User
    {
        $user = new User();
        $company = new Company();
        $location = new Location();
        $company->setLocation($location);
        $user->setCompany($company);
        $user->setRoles(['ROLE_ADMIN']);
        foreach (self::COMPANY_CONFIGURATIONS as $name => $type) {
            $configuration = new Configuration();
            $configuration->setType($type);
            $configuration->setName($name); 
            $configuration->setCompany($company);
            $company->addConfiguration($configuration);
        }

        return $user;
    }

    public function createEmployee(Company $company): User
    {
        $user = new User();
        $user->setCompany($company);
        $user->setRoles(['ROLE_EMPLOYEE']);
        return $user;
        // create employee
    }
}