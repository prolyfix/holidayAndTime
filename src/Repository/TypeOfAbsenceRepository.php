<?php

namespace App\Repository;

use App\Entity\TypeOfAbsence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeOfAbsence>
 *
 * @method TypeOfAbsence|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOfAbsence|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOfAbsence[]    findAll()
 * @method TypeOfAbsence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOfAbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeOfAbsence::class);
    }

    //    /**
    //     * @return TypeOfAbsence[] Returns an array of TypeOfAbsence objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeOfAbsence
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
