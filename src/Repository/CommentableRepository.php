<?php

namespace App\Repository;

use App\Entity\Commentable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentable>
 */
class CommentableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentable::class);
    }

    public function retrieveCommentables($name): array
    {
        $output = [];
        $values  = $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();

        foreach($values as $value){
            if(method_exists($value, 'getName')){
                if(strpos($value->getName(),$name)!==false){
                    $output[] = $value;
                }
            }
        }   
        return $output;
    }
}