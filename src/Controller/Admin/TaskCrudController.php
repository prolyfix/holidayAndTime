<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Task;
use App\Form\CommentType;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskCrudController extends AbstractCrudController
{

    
    public function configureActions(Actions $actions): Actions
    {
        $action = Action::new('addComment', 'Add Comment', 'fa fa-comment')
            ->linkToCrudAction('addComment');
        $actionAddMedia = Action::new('addMedia', 'Add Media', 'fa fa-image')
            ->linkToCrudAction('addMedia');
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, $action)
            ->add(Crud::PAGE_DETAIL, $actionAddMedia)
            ->add(Crud::PAGE_DETAIL, $action)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
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
        foreach ($tasks as $task) {
            if ($task->getStatus() === 'todo') {
                $todo[] = $task;
            } elseif ($task->getStatus() === 'in_progress') {
                $inProgress[] = $task;
            } elseif ($task->getStatus() === 'done') {
                $done[] = $task;
            }
        }
        return $this->render('admin/task/kanban.html.twig', [
            'todos' => $todo,
            'inProgress' => $inProgress,
            'done' => $done,
        ]);
    }

    public function addComment(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Task::class)->find($entityId);
        $comment = new Comment();
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
        ;
    }


    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        yield IdField::new('id')->hideOnForm();
        if($user->getCompany()->getConfiguration('hasProject')->getValue()){
            yield AssociationField::new('project');
            //yield AssociationField::new('project.ThirdParty')->setLabel('customer')->hideOnForm();
        }
        yield TextField::new('name');
        yield AssociationField::new('assignedTo')->setFormTypeOption('query_builder', function ($entity) use ($user) {
            return $entity->createQueryBuilder('m')
                ->andWhere('m.company = :company')
                ->setParameter('company', $user->getCompany());
        });
        yield TextEditorField::new('description');
        yield AssociationField::new('media')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating();
        yield ChoiceField::new('status')->setChoices([
            'todo' => 'todo',
            'in_progress' => 'in_progress',
            'done' => 'done',
        ]);
        yield AssociationField::new('comments')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating()->setTemplatePath('admin/comment/field.html.twig');
    }
    
}
