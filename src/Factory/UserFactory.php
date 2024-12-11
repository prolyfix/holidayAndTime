<?php
namespace App\Factory;

use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\Location;
use App\Entity\TypeOfAbsence;
use App\Entity\User;
use App\Entity\UserSchedule;

class UserFactory
{
    const COMPANY_CONFIGURATIONS = [
        'task'=>['active'=>'bool'],
        'hasWeekplan'=>['active'=>'bool'],
        'hasProject'=>['active'=>'bool'],
        'hasRoomPlan'=>['active'=>'bool'],
        'hasCalendar'=>['active'=>'bool'],
        'thresholdHalfDay'=>'float',
        'hasCRM'=>'bool',
        'hasApointment'=>'bool',
    ];
    const COMPANY_DEFAULT_TYPE_OF_ABSENCES = [
    'vacation'=>[
        'isHoliday'=>true,
        'isTimeHoliday'=>false,
        'hasToBeValidated'=>true,
        'isBankHoliday'=>false,
        'isWorkingDay'=>false
    ]
    ,'sick'=>[
        'isHoliday'=>false,
        'isTimeHoliday'=>false,
        'hasToBeValidated'=>false,
        'isBankHoliday'=>false,
        'isWorkingDay'=>false
    ]
    ,'unpaid'=>[
        'isHoliday'=>false,
        'isTimeHoliday'=>false,
        'hasToBeValidated'=>true,
        'isBankHoliday'=>true,
        'isWorkingDay'=>false
    ]
    ,'timeHoliday'=>[
        'isHoliday'=>false,
        'isTimeHoliday'=>true,
        'hasToBeValidated'=>true,
        'isBankHoliday'=>false,
        'isWorkingDay'=>false
    ],
    'bankHoliday'=>[
        'isHoliday'=>true,
        'isTimeHoliday'=>false,
        'hasToBeValidated'=>true,
        'isBankHoliday'=>true,
        'isWorkingDay'=>false
    ],
    'training'=>[
        'isHoliday'=>false,
        'isTimeHoliday'=>false,
        'hasToBeValidated'=>true,
        'isBankHoliday'=>false,
        'isWorkingDay'=>true
    ],
    ];


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
        foreach(self::COMPANY_DEFAULT_TYPE_OF_ABSENCES as $name=>$values){
            $typeOfAbsence = new TypeOfAbsence();
            $typeOfAbsence->setName($name);
            $typeOfAbsence->setCompany($company);
            foreach ($values as $key=>$value) {
                $function = 'set'.ucfirst($key);
                $typeOfAbsence->$function($value);
            }
            $company->addTypeOfAbsence($typeOfAbsence);
        }
        return $user;
    }

    public function createEmployee(Company $company): User
    {
        $user = new User();
        $user->setCompany($company);
        $user->setRoles(['ROLE_EMPLOYEE']);
        $userSchedule = new UserSchedule();
        return $user;
        // create employee
    }
}