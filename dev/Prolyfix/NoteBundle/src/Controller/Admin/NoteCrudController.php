<?php

namespace Prolyfix\NoteBundle\Controller\Admin;

use App\Controller\Admin\BaseCrudController;
use Prolyfix\NoteBundle\Entity\Note;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NoteCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Note::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date'),
            TextField::new('note'),
        ];
    }
    
}
