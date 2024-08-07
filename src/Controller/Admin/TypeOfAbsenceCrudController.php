<?php

namespace App\Controller\Admin;

use App\Entity\TypeOfAbsence;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
        ];
    }
    
}
