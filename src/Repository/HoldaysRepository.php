<?php

namespace App\Repository;

use App\Entity\Holdays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Holdays>
 *
 * @method Holdays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holdays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holdays[]    findAll()
 * @method Holdays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoldaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holdays::class);
    }

    //    /**
    //     * @return Holdays[] Returns an array of Holdays objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Holdays
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
