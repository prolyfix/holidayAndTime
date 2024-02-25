<?php

namespace App\Repository;

use App\Entity\UserWeekdayProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWeekdayProperty>
 *
 * @method UserWeekdayProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWeekdayProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWeekdayProperty[]    findAll()
 * @method UserWeekdayProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWeekdayPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWeekdayProperty::class);
    }

    //    /**
    //     * @return UserWeekdayProperty[] Returns an array of UserWeekdayProperty objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserWeekdayProperty
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
