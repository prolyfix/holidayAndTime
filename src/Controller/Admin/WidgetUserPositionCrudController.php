<?php

namespace App\Controller\Admin;

use App\Entity\WidgetUserPosition;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WidgetUserPositionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WidgetUserPosition::class;
    }

    public function configureWidgetPositions()
    {
        return $this->render('admin/widget_position/index.html.twig');
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
