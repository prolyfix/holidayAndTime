<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\ModuleConfigurationValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleConfigurationValue>
 */
class ModuleConfigurationValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleConfigurationValue::class);
    }


    public function findFromModule($params)
    {
        return $this->createQueryBuilder('m')
                ->join('m.moduleConfiguration','moduleConfiguration')
                ->andWhere('m.relatedClass = :relatedClass')
                ->andWhere('m.relatedId = :relatedId')
                ->setParameter('relatedClass', $params['relatedClass'])
                ->setParameter('relatedId', $params['relatedId'])
                ->join('moduleConfiguration.module','module')
                ->andWhere('module.id = :module')
                ->setParameter('module', $params['moduleId'])
                ->getQuery()
                ->getResult()

        ;
    }

    public function getActiveModules(Company $company)
    {
        $values = $this->createQueryBuilder('m')
                    ->andWhere('m.relatedClass = :relatedClass')
                    ->setParameter('relatedClass', Company::class)
                    ->andWhere('m.relatedId = :relatedId')
                    ->setParameter('relatedId', $company->getId())
                    ->getQuery()
                    ->getResult();
        return $values;
    }
}
