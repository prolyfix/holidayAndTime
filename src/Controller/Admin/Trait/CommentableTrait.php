<?php
namespace App\Controller\Admin\Trait;


use App\Entity\Comment;
use App\Entity\Commentable;
use App\Entity\Media;
use App\Entity\Timesheet;
use App\Form\CommentType;
use App\Form\MediaType;
use App\Form\TimesheetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

Trait CommentableTrait{

    public function addComment(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Commentable::class)->find($entityId);
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setCommentable($entity);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
        $entity = $em->getRepository(Commentable::class)->find($entityId);
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

    public function addTimesheet(EntityManagerInterface $em, Request $request)
    {
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Commentable::class)->find($entityId);
        $timesheet = new Timesheet();
        $timesheet->setRelatedCommentable($entity);
        $form = $this->createForm(TimesheetType::class, $timesheet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($timesheet);
            $em->flush();
            $className = explode('\\',$entity::class);

            return $this->redirectToRoute('admin',[
                'crudAction' => 'detail',
                'entityId' => $entityId,
                'crudControllerFqcn' => 'App\Controller\Admin\\'.end($className).'CrudController',
            ]);
        }
        return $this->render('admin/comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function startWorking(EntityManagerInterface $em, Request $request)
    {
        $this->em->getRepository(Timesheet::class)->stopWorking($this->getUser());
        $entityId = $request->get('entityId');
        $entity = $em->getRepository(Commentable::class)->find($entityId);
        $timesheet = new Timesheet();
        $timesheet->setRelatedCommentable($entity);
        $timesheet->setStartTime(new \DateTime());
        $timesheet->setUser($this->getUser());  
        $em->persist($timesheet);
        $em->flush();
        $className = explode('\\',$entity::class);
        return $this->redirectToRoute('admin',[
            'crudAction' => 'detail',
            'entityId' => $entityId,
            'crudControllerFqcn' => 'App\Controller\Admin\\'.end($className).'CrudController',
        ]);
    }



}