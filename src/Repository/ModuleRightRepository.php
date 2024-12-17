<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\ModuleRight;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleRight>
 */
class ModuleRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleRight::class);
    }
    public function findFromModule($module, $company)
    {
        return $this->createQueryBuilder('m')
                ->join('m.module','module')
                ->andWhere('m.appliedToCompany = :appliedToCompany')
                ->andWhere('module.class = :module')
                ->setParameter('module', $module)
                ->setParameter('appliedToCompany', $company)
                ->getQuery()
                ->getResult()

        ;
    }

    public function getModuleRightsForUserAndTable(Module $module, $entityClass,User $user, string $action = 'list')

    {
        $qb = $this->createQueryBuilder('m')
                ->join('m.module','module')
                ->andWhere('module.id = :module')
                ->setParameter('module', $module)
                ->andWhere('m.entityClass = :entityClass')
                ->setParameter('entityClass', $entityClass)
                ->andWhere('m.appliedToCompany = :appliedToCompany')
                ->setParameter('appliedToCompany', $user->getCompany())
                ->andWhere('m.role = :role')
                ->setParameter('role', $user->getRoles()[0])
                ->andWhere('m.moduleAction like :action')
                ->setParameter('action', '%'.$action.'%')
                ->getQuery()
                ->getResult();

        return $qb;
    }
}
