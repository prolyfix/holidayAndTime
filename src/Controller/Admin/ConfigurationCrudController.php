<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Form\ConfigurationType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ConfigurationCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator, private EntityManagerInterface $em)
    {
    }
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('value'),
            ChoiceField::new('type')->hideOnIndex()->setChoices([
                'string' => 'string',
                'int' => 'int',
                'float' => 'float',
                'bool' => 'bool',
                'array' => 'array',
            ]),
        ];
    }

    public function showConfiguration()
    {
        return $this->render('admin/configuration/show.html.twig',['page_title'=>'Configuration','content_title'=>'Configuration']);
    }

    public function edit(AdminContext $context)
    {
        $context->getEntity()->setActions(ActionCollection::new([]));
        $form = $this->createForm(ConfigurationType::class, $context->getEntity()->getInstance());
        $form->handleRequest($context->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirect($this->adminUrlGenerator
                ->setController(ConfigurationCrudController::class)
                ->setAction('showConfiguration')
                ->generateUrl());
        }

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'pageName' => Crud::PAGE_EDIT,
            'templateName' => 'crud/edit',
            'edit_form' => $form,
            'entity' => $context->getEntity(),
        ]));

        return $responseParameters;
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
