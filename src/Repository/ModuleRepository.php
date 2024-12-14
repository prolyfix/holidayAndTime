<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function ListModulesNotRegistered($availableModules): array
    {
        $qb = $this->createQueryBuilder('m');
        $list = $qb->getQuery()->getResult();
        foreach($list as $module)
        {
            if(array_key_exists($module->getName(),$availableModules))
            {
                unset($availableModules[$module->getName()]);
            }
        }
        return $availableModules;
    }

}
