<?php

namespace App\Repository;

use App\Entity\HelpContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpContent>
 */
class HelpContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpContent::class);
    }


    public function retrieveHelp(string $path): ?HelpContent
    {
        return $this->createQueryBuilder('h')
            ->andWhere(':path LIKE CONCAT(\'%\',h.route,\'%\')')
            ->setParameter('path', $path)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }   
//    /**
//     * @return HelpContent[] Returns an array of HelpContent objects
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

//    public function findOneBySomeField($value): ?HelpContent
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
