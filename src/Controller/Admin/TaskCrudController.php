<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TaskCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        yield IdField::new('id')->hideOnForm();
        if($user->getCompany()->getConfiguration('hasProject')->getValue()){
            yield AssociationField::new('project');
        }
        yield TextField::new('name');
        yield AssociationField::new('assignedTo')->setFormTypeOption('query_builder', function ($entity) use ($user) {
            return $entity->createQueryBuilder('m')
                ->andWhere('m.company = :company')
                ->setParameter('company', $user->getCompany());
        });
    }
    
}
