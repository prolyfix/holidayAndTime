<?php

namespace App\Controller\Admin;

use App\Entity\ThirdParty;
use App\Form\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class ThirdPartyCrudController extends AbstractCrudController
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em) {}

    public static function getEntityFqcn(): string
    {
        return ThirdParty::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            AssociationField::new('contacts')->setTemplatePath('admin/contact/field.html.twig'),
            AssociationField::new('projects')->setTemplatePath('admin/project/field.html.twig'),
            AssociationField::new('location')->setFormType(LocationType::class),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->query->get('entityId')) {
            $entityId = $request->query->get('entityId');
            $entity = $this->em->getRepository(ThirdParty::class)->find($entityId);
            $crud->setPageTitle(Crud::PAGE_DETAIL, $entity->getName());
        }
        //$crud->setPageTitle(Crud::PAGE_DETAIL, 'coucou');
        return $crud;
    }

}
