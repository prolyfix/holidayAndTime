<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Project;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ProjectCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $crudUrlGenerator, private RequestStack $requestStack, private EntityManagerInterface $em)
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
            AssociationField::new('media')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/media/field.html.twig'),
            AssociationField::new('tasks')->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/task/field.html.twig'),
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $createTaskAction = Action::new('createTask', 'Create Task', 'fa fa-plus')
        ->linkToCrudAction('createTask');
        $actionAddMedia = Action::new('addMedia', 'Add Media', 'fa fa-image')
        ->linkToCrudAction('addMedia');
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $createTaskAction)
            ->add(Crud::PAGE_DETAIL, $actionAddMedia)
        ;
    }
    public function addMedia(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Project::class)->find($entityId);
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $media->setCommentable($entity);
            $em->persist($media);
            $em->flush();
            return $this->redirectToRoute('admin',[
                'crudAction' => 'detail',
                'entityId' => $entityId,
                'crudControllerFqcn' => 'App\Controller\Admin\ProjectCrudController',
            ]);
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);

    } 

    public function configureCrud(Crud $crud): Crud
    {
        $request = $this->requestStack->getCurrentRequest();
        if($request->query->get('entityId')){
            $entityId = $request->query->get('entityId');
            $entity = $this->em->getRepository(Project::class)->find($entityId);
            $crud->setPageTitle(Crud::PAGE_DETAIL, $entity->getName());
        }
        //$crud->setPageTitle(Crud::PAGE_DETAIL, 'coucou');
        return $crud;
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
