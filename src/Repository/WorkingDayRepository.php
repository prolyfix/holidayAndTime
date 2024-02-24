<?php

namespace App\Repository;

use App\Entity\WorkingDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkingDay>
 *
 * @method WorkingDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingDay[]    findAll()
 * @method WorkingDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingDay::class);
    }

//    /**
//     * @return WorkingDay[] Returns an array of WorkingDay objects
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

//    public function findOneBySomeField($value): ?WorkingDay
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
