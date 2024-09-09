<?php

namespace App\Controller\Admin;

use App\Entity\TypeOfAbsence;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class TypeOfAbsenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeOfAbsence::class;
    }

    public function createIndexQueryBuilder($searchDto,  $entityDto, $fields, $filters): QueryBuilder
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $queryBuilder = parent::createIndexQueryBuilder( $searchDto,  $entityDto,  $fields,  $filters);
        
        if($user->hasRole('SUPER_ADMIN')){
            return $queryBuilder;
        }

        $queryBuilder->andwhere('entity.company = :company')
            ->setParameter('company', $user->getCompany());

        return $queryBuilder;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            BooleanField::new('isHoliday'),
            BooleanField::new('isTimeHoliday'),
            BooleanField::new('hasToBeValidated'),
            BooleanField::new('isBankHoliday'),
            BooleanField::new('isWorkingDay'),
            TextField::new('shortTitle'),
            ColorField::new('color'),
        ];
    }
    
}
