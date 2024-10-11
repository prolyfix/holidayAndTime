<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\CommentableTrait;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Project;
use App\Entity\Task;
use App\Form\CommentType;
use App\Form\MediaType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

class TaskCrudController extends AbstractCrudController
{

    use CommentableTrait;

    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em)
    {
    }

    public function configureActions(Actions $actions): Actions
    {
        $createTaskAction = Action::new('createTask', 'Create Task', 'fa fa-plus')
        ->linkToCrudAction('createTask');
        $actionAddMedia = Action::new('addMedia', 'Add Media', 'fa fa-image')
        ->linkToCrudAction('addMedia');
        $actionComment = Action::new('addComment', 'Add Comment', 'fa fa-comment')
        ->linkToCrudAction('addComment');
        $actionTimesheet = Action::new('addTimesheet', 'Add Timesheet', 'fa fa-clock')
        ->linkToCrudAction('addTimesheet');
        $actionStartWorking = Action::new('startWorking', 'Start Working', 'fa fa-play')
        ->linkToCrudAction('startWorking');
        $actionKanboard = Action::new('kanban', 'Kanban', 'fa fa-tasks')
            ->linkToCrudAction('kanban')->createAsGlobalAction();
        return $actions
            ->add(Crud::PAGE_INDEX, $actionKanboard)
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
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function kanban(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $tasks = $em->getRepository(Task::class)->findBy(['assignedTo' => $user]);
        $todo = [];
        $inProgress = [];
        $done = [];
        $new = [];
        foreach ($tasks as $task) {
            if ($task->getStatus() === 'todo') {
                $todo[] = $task;
            } elseif ($task->getStatus() === 'in_progress') {
                $inProgress[] = $task;
            } elseif ($task->getStatus() === 'done') {
                $done[] = $task;
            }elseif ($task->getStatus() === 'new') {
                $new[] = $task;
            }
        }
        return $this->render('admin/task/kanban.html.twig', [
            'todos' => $todo,
            'inProgress' => $inProgress,
            'done' => $done,
            'news' => $new,
        ]);
    }

    public function addComment(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Task::class)->find($entityId);
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCommentable($entity);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('admin',[
                'crudAction' => 'detail',
                'entityId' => $entityId,
                'crudControllerFqcn' => 'App\Controller\Admin\TaskCrudController',
            ]);
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    public function createEntity(string $entityFqcn)
    {
        $request = $this->requestStack->getCurrentRequest();

        $projectId = $request->query->get('project');

        $task = new Task();
        $task->setAssignedTo($this->getUser());
        if ($projectId) {
            $project = $this->em->getRepository(Project::class)->find($projectId);
            
            if ($project) {
                $task->setProject($project);
            }
        }

        return $task;
    }

    public function addMedia(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Task::class)->find($entityId);
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
                'crudControllerFqcn' => 'App\Controller\Admin\TaskCrudController',
            ]);
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);

    } 
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('project')
            ->add('name')
            ->add(ChoiceFilter::new('status')->setChoices([
                'new' => 'new',
                'todo' => 'todo',
                'in_progress' => 'in_progress',
                'done' => 'done',
            ]))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $request = $this->requestStack->getCurrentRequest();
        if($request->query->get('entityId')){
            $entityId = $request->query->get('entityId');
            $entity = $this->em->getRepository(Task::class)->find($entityId);
            $crud->setPageTitle(Crud::PAGE_DETAIL, $entity->getName());
        }
        //$crud->setPageTitle(Crud::PAGE_DETAIL, 'coucou');
        return $crud;
    }



    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        yield FormField::addTab('Allgemein', 'start')->hideOnForm();
        yield IdField::new('id')->hideOnForm();
        if($user->getCompany()!==null && $user->getCompany()->getConfiguration('hasProject')->getValue()){
            yield AssociationField::new('project');
            //yield AssociationField::new('project.ThirdParty')->setLabel('customer')->hideOnForm();
        }
        yield DateField::new('dueDate');
        yield AssociationField::new('tags');

        yield TextField::new('name')->hideOnDetail()->setCustomOption('tab', 'start');
        yield AssociationField::new('assignedTo')->setFormTypeOption('query_builder', function ($entity) use ($user) {
            return $entity->createQueryBuilder('m')
                ->andWhere('m.company = :company')
                ->setParameter('company', $user->getCompany());
        });
        yield TextEditorField::new('description')->setTemplatePath('admin/fields/raw_text_editor.html.twig');
        yield AssociationField::new('media')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/media/field.html.twig');
        yield ChoiceField::new('status')->setChoices([
            'new' => 'new',
            'todo' => 'todo',
            'in_progress' => 'in_progress',
            'done' => 'done',
        ]);
        yield FormField::addTab('Timesheet', 'start')->hideOnForm();
        yield AssociationField::new('relatedTimesheets')->hideOnForm()->setTemplatePath('admin/timesheet/field.html.twig');
        yield FormField::addTab('Comments', 'start')->hideOnForm();
        yield AssociationField::new('comments')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/comment/field.html.twig');
    }
    
}
