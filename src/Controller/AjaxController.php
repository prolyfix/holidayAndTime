<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Commentable;
use App\Entity\Task;
use App\Entity\Timesheet;
use App\Utility\TimeUtility;
use App\Entity\WidgetUserPosition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/ajax', name: 'app_ajax')]
class AjaxController extends AbstractController
{
    #[Route('/start', name: 'app_ajax_start', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $timesheet = $em->getRepository(Timesheet::class)->findOneBy(['user' => $this->getUser(), 'endTime' => null]);
        if ($timesheet) {
            $timesheet->setIsBreak(false);
            $em->flush();
            return new JsonResponse(['statut' => 'error', 'message' => 'Timer already started']);
        }
        $timesheet = (new Timesheet())
            ->setStartTime(new \DateTime('now'))
            ->setUser($this->getUser());
        $em->persist($timesheet);
        $em->flush();

        return new JsonResponse(['statut' => 'ok', 'message' => 'Timer started', 'data' => ['start' => date('Y-m-d H:i:s')]]);
    }
    #[Route('/stop', name: 'app_ajax_stop', methods: ['POST'])]
    public function stop(Request $request, EntityManagerInterface $em): Response
    {
        $timesheet = $em->getRepository(Timesheet::class)->findOneBy(['user' => $this->getUser(), 'endTime' => null]);
        $times = json_decode($request->getContent(), true);
        $timesheet->setWorkingMinutes($times['elapsedTime'] / 60 / 1000);
        $timesheet->setEndTime(new \DateTime('now'));
        $em->flush();
        return new JsonResponse(['statut' => 'ok', 'message' => 'Timer stopped', 'data' => ['stop' => date('Y-m-d H:i:s')]]);
    }

    #[Route('/retrieveElapsedTime', name: 'app_ajax_retrieve', methods: ['GET'])]
    public function retrieve(EntityManagerInterface $em): Response
    {
        $timesheet = $em->getRepository(Timesheet::class)->findOneBy(['user' => $this->getUser(), 'endTime' => null]);
        if(!$timesheet){
            return new JsonResponse(['statut' => 'error', 'message' => 'No timer started','elapsedTime' => 0]);
        }
        $actualWorkingMinutes = $timesheet->getWorkingMinutes();
        if($actualWorkingMinutes == null){
            $overTime = (new \DateTime())->diff($timesheet->getStartTime());
            $actualWorkingMinutes = TimeUtility::getMinutesFromDateInterval($overTime);
        }
        if(!$timesheet->isBreak()){
            $timeAfterLastUpdate = (new \DateTime())->diff($timesheet->getUpdateDatetime()??new \DateTime());
            $actualWorkingMinutes += TimeUtility::getMinutesFromDateInterval($timeAfterLastUpdate);
        }
        return new JsonResponse([
            'statut' => 'ok', 
            'message' => 'Data retrieved', 
            'elapsedTime' => $actualWorkingMinutes, 
            'isBreak' => $timesheet->isBreak(),
            'commentable' => $timesheet->getRelatedCommentable()!== null ? $timesheet->getRelatedCommentable()->getName():null,
            'commentableId' =>$timesheet->getRelatedCommentable()!== null ? $timesheet->getRelatedCommentable()->getId():null,
        
        ]);
    }

    #[Route('/break', name: 'app_ajax_break', methods: ['POST'])]
    public function break(EntityManagerInterface $em, Request $request): Response
    {
        $timesheet = $em->getRepository(Timesheet::class)->findOneBy(['user' => $this->getUser(), 'endTime' => null]);
        $times = json_decode($request->getContent(), true);
        //$times = $request->get('elapsedTime');
        $timesheet->setWorkingMinutes($times['elapsedTime'] / 60 / 1000);
        $timesheet->setUpdateDatetime(new \DateTime('now'));
        $timesheet->setIsBreak($times['isBreak']);
        $em->flush();
        return new JsonResponse(['statut' => 'ok', 'message' => 'Break started', 'data' => ['start' => date('Y-m-d H:i:s')]]);
    }

    #[Route('/retrieveList', name: 'app_ajax_retrieve_list', methods: ['POST'])]
    public function retrieveList(EntityManagerInterface $em, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $output = [];
        $data = json_decode($request->getContent(), true);
        $commentables = $em->getRepository(Commentable::class)->retrieveCommentables( $data['commentable'] ?? null);
        foreach($commentables as $commentable){
            $output[] = [
                'id' => $commentable->getId(),
                'name' => $commentable->getName(),
                'type' => $commentable::class,
            ];
        }
        return new JsonResponse($output);
    }

    #[Route('/saveComment', name: 'app_ajax_save_comment', methods: ['POST'])]
    public function saveComment(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commentable = $em->getRepository(Commentable::class)->findOneById($data['commentableId']);
        $comment = new Comment();
        $comment->setCommentable($commentable);
        $comment->setUser($this->getUser());
        $comment->setComment($data['comment']);
        dump($comment);
        $em->persist($comment);
        $em->flush();
        dump($comment);
        return new JsonResponse(['statut' => 'ok', 'message' => 'Comment saved']);
    }

    #[Route('/startWorking', name: 'app_ajax_start_working', methods: ['POST'])]
    public function startWorking(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commentable = $em->getRepository(Commentable::class)->find($data['commentable']);
        $comment = new Comment();
        $comment->setCommentable($commentable);
        $comment->setUser($this->getUser());
        $comment->setComment("Startet arbeiten");

        $actualTimesheet = $em->getRepository(Timesheet::class)->findOneBy(['user' => $this->getUser(), 'endTime' => null]);
        if($actualTimesheet){
            $actualTimesheet->setEndTime(new \DateTime('now'));
            $em->flush();
        }
        $timesheet = (new Timesheet())
            ->setStartTime(new \DateTime('now'))
            ->setUser($this->getUser())
            ->setRelatedCommentable($commentable);
        $em->persist($timesheet);
        $em->persist($comment);
        $em->flush();
        return new JsonResponse(['statut' => 'ok', 'message' => 'Working started', 'data' => ['start' => date('Y-m-d H:i:s')]]);
    }

    #[Route('/update-task-state', name: 'update_task_state', methods: ['POST'])]
    public function updateTaskState(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        //ameliorer
        $table =["New"=>"new","To Do"=>"todo", "In Progress"=>"in_progress", "Done"=>"done"];
        $data = json_decode($request->getContent(), true);
        $taskId = $data['taskId'];
        $newState = $data['newState'];

        $task = $entityManager->getRepository(Task::class)->find($taskId);
        if (!$task) {
            return new JsonResponse(['success' => false, 'message' => 'Task not found'], 404);
        }
        dump($newState);
        $task->setStatus($table[$newState]);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/newWidgetPos', name: 'new_widget_position', methods: ['POST'])]
    public function newWidgetPos(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $widget = $data['widgetId'];
        $row = $data['row'];
        $column = $data['column'];
        $user = $this->getUser();
        $widgetUserPosition = new  WidgetUserPosition();
        $widgetUserPosition->setUser($user);
        $widgetUserPosition->setWidgetClass($widget);
        $widgetUserPosition->setRowIndex($row);
        $widgetUserPosition->setColumIndex($column);
        $widgetUserPosition->setCrudAction('index');
        $widgetUserPosition->setCrudControllerFqcn('App\Controller\Admin\DashboardController');
        $entityManager->persist($widgetUserPosition);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
    }
}
