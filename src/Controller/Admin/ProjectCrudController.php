<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProjectCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $crudUrlGenerator)
    {
    }
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            DateTimeField::new('creationDate')->hideOnForm(),
            AssociationField::new('thirdParty'),
            AssociationField::new('tasks')->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/task/field.html.twig')
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $createTaskAction = Action::new('createTask', 'Create Task', 'fa fa-plus')
        ->linkToCrudAction('createTask');
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $createTaskAction)
        ;
    }
    public function createTask(AdminContext $context): RedirectResponse
    {
        $projectId = $context->getEntity()->getPrimaryKeyValue();
        $url = $this->crudUrlGenerator
               ->setController(TaskCrudController::class)
                ->setAction('new')
                ->removeReferrer()
                ->generateUrl();
        $url = str_replace('entityId','project',$url);
        return $this->redirect($url);
    }
}
