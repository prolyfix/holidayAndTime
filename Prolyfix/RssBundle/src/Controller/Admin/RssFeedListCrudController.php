<?php

namespace Prolyfix\RssBundle\Controller\Admin;

use App\Controller\Admin\BaseCrudController;
use Prolyfix\RssBundle\Entity\RssFeedList;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RssFeedListCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return RssFeedList::class;
    }
}
