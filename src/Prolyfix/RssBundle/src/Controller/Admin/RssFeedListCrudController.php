<?php

namespace Prolyfix\RssBundle\Controller\Admin;

use Prolyfix\RssBundle\Entity\RssFeedList;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RssFeedListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RssFeedList::class;
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
