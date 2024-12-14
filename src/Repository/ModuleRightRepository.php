<?php

namespace App\Repository;

use App\Entity\ModuleRight;
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
    //    /**
    //     * @return ModuleRight[] Returns an array of ModuleRight objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ModuleRight
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
