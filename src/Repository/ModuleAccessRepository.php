<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\ModuleAccess;
use App\Entity\User;
use App\Entity\WorkingGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleAccess>
 */
class ModuleAccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleAccess::class);
    }

    public function hasUserAccessToModule(User $user, Module $module):bool
    {
        $qb = $this->createQueryBuilder('m')
                    ->andWhere('m.tenantClass = :tenantUser and m.tenantId = :tenantUserId')
                    ->orWhere('m.tenantClass = :tenantCompany and m.tenantId = :tenantCompanyId')
                    ->setParameter('tenantUser', User::class)
                    ->setParameter('tenantUserId', $user->getId())
                    ->setParameter('tenantCompany', Company::class)
                    ->setParameter('tenantCompanyId', $user->getCompany()->getId())
                    ->andWhere('m.module = :module')
                    ->setParameter('module', $module);
        
        if($user->getWorkingGroup() !== null)
        {
            $qb->orWhere('m.tenantClass = :tenantWorkingGroup and m.tenantId = :tenantWorkingGroupId')
                ->setParameter('tenantWorkingGroup', WorkingGroup::class)
                ->setParameter('tenantWorkingGroupId', $user->getWorkingGroup()->getId());
        }
                    
        $values = $qb->getQuery()
                    ->getResult();



        return count($values) > 0;
    }

}
