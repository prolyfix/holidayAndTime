<?php

namespace App\Controller\Admin;

use App\Entity\ModuleConfigurationValue;
use App\Manager\ConfigurationUpdater;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ModuleConfigurationValueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModuleConfigurationValue::class;
    }

    public function showConfiguration(ConfigurationUpdater $configurationUpdater)
    {
        $configurationUpdater->getConfigurationList();
        dump("ici");
        return $this->render('admin/configuration/show.html.twig',['page_title'=>'Configuration','content_title'=>'Configuration']);
    }
}
