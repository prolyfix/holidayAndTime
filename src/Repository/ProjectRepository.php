<?php

namespace App\Repository;

use App\Entity\Commentable;
use App\Entity\Company;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends CommentableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function retrieveCommentablesFromCompany(Company $company): iterable
    {
        $values  = parent::retrieveCommentablesFromCompany($company);
        $output = [];
        foreach($values as $value)
        {
            if($value instanceof Project)
            {
                $output[] = $value;
            }
        }
        return $output;
    }
}
