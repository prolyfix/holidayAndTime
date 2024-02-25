<?php

namespace App\Repository;

use App\Entity\WorkingGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkingGroup>
 *
 * @method WorkingGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingGroup[]    findAll()
 * @method WorkingGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingGroup::class);
    }

    //    /**
    //     * @return WorkingGroup[] Returns an array of WorkingGroup objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkingGroup
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
