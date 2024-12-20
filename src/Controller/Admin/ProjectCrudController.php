<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\CommentableTrait;
use App\Entity\Media;
use App\Entity\Project;
use Prolyfix\TimesheetBundle\Entity\Timesheet;
use App\Entity\User;
use App\Form\ConsumeTimeType;
use App\Form\MediaType;
use App\Form\TimesheetType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;

class ProjectCrudController extends AbstractCrudController
{
    use CommentableTrait;
    public function __construct(private AdminUrlGenerator $crudUrlGenerator, private RequestStack $requestStack, private EntityManagerInterface $em) {}
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            FormField::addTab('Allgemein', 'start'),
            IdField::new('id')->hideOnForm(),
            UrlField::new('name')->formatValue(function ($value, $entity) {
                if($entity !== null)
                    return '<a href="/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CProjectCrudController&entityId=' . $entity->getId() . '">' . $value . '</a>';
                return  $value;
            })->hideOnDetail(),
            DateTimeField::new('creationDate')->hideOnForm(),
            AssociationField::new('thirdParty'),
            AssociationField::new('media')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/media/field.html.twig'),
            AssociationField::new('members')->setTemplatePath('admin/user/field.html.twig'),
            FormField::addTab('Tasks', 'start'),
            AssociationField::new('tasks')->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/task/field.html.twig'),
            FormField::addTab('Timesheet', 'start'),
            ArrayField::new('relatedTimesheetsIncludedTasks')->hideOnForm()->hideOnIndex()->setTemplatePath('admin/timesheet/field.html.twig'),
            FormField::addTab('Comments', 'start'),
            AssociationField::new('comments')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/comment/field.html.twig'),

        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $createTaskAction = Action::new('createTask', 'Create Task', 'fa fa-plus')
            ->linkToCrudAction('createTask');
        $actionAddMedia = Action::new('addMedia', 'Add Media', 'fa fa-image')
            ->linkToCrudAction('addMedia')->setCssClass('btn sidebar-action');
        $actionComment = Action::new('addComment', 'Add Comment', 'fa fa-comment')
            ->linkToCrudAction('addComment')->setCssClass('btn sidebar-action');
        $actionTimesheet = Action::new('addTimesheet', 'Add Timesheet', 'fa fa-clock')
            ->linkToCrudAction('addTimesheet')->setCssClass('btn sidebar-action');
        $actionStartWorking = Action::new('startWorking', 'Start Working', 'fa fa-play')
            ->linkToCrudAction('startWorking');
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $actionComment)
            ->add(Crud::PAGE_DETAIL, $actionAddMedia)
            ->add(Crud::PAGE_DETAIL, $actionTimesheet)
            ->add(Crud::PAGE_DETAIL, $actionStartWorking)
            ->add(Crud::PAGE_DETAIL, $createTaskAction)
            ->add(Crud::PAGE_INDEX, $actionComment)
            ->add(Crud::PAGE_INDEX, $actionAddMedia)
            ->add(Crud::PAGE_INDEX, $actionTimesheet)
            ->add(Crud::PAGE_INDEX, $actionStartWorking)
            ->add(Crud::PAGE_INDEX, $createTaskAction)
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
            return $this->redirectToRoute('admin', [
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
        if ($request->query->get('entityId')) {
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
        $url = str_replace('entityId', 'project', $url);
        return $this->redirect($url);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser();
        $query = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $projects = $user->getCommentableMembers();
        if (count($projects) > 0) {
            $query->join('entity.members', 'member')
            ->andWhere(':user MEMBER OF entity.members')
            ->setParameter('user', $user);
        }
        $query->join('entity.createdBy', 'user')
            ->orWhere('user.company = :company')
            ->setParameter('company', $user->getCompany());

        return $query;
    }
}
