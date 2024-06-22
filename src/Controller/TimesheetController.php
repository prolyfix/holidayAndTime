<?php

namespace App\Controller;

use App\Entity\Timesheet;
use App\Form\TimesheetType;
use App\Manager\OvertimeCalculator;
use App\Repository\TimesheetRepository;
use App\Utility\TimeUtility;
use Doctrine\ORM\EntityManagerInterface;
use Prolyfix\SymfonyDatatablesBundle\Controller\DatatablesController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/timesheet')]
class TimesheetController extends AbstractController
{
    #[Route('/', name: 'app_timesheet_index', methods: ['GET'])]
    public function index(TimesheetRepository $timesheetRepository): Response
    {
        $tableParams = [ 
            'tableId' => 'history', 
            'targetEntity' => Timesheet::class, 
            'columns' => [ 
                ['name' =>  'startTime',    
                  'label' => 'startDate',
                ],  
                ['name' =>  'endTime',    
                'label' => 'endDate',
                ], 
                ['name' =>  'user.email',    
                'label' => 'user',
                ],   
                ['name' =>  'overtime',    
                'label' => '+/- Minuten',
                ],                                
                ['name' =>  '_action',    
                'label' => 'actions',
                ],                 
            ], 
            'params' => ['order' => [[1,'desc']]], 
            'tableTitle' => 'users' ,
            
        ];
        $buttons = [
            'new' => [
                'class' => 'btn btn-primary',
                'icon' => 'bi bi-plus',
                'title' => 'New',
                'action' => 'click->hello#link',
                'url' =>  $this->generateUrl('app_timesheet_new')
            ]
        ];
        return $this->render('gestion.html.twig', [
            'tableParams' => $tableParams,
            'filter'    => Timesheet::class,
            'title' => 'Timesheet',
            'buttons' => $buttons
        ]);
    }

    #[Route('/new', name: 'app_timesheet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timesheet = new Timesheet();
        $form = $this->createForm(TimesheetType::class, $timesheet);
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $timesheet->setUser($this->getUser());
            $form->remove('user');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // if is not under contract, we add directly the overtime
            $overTime = $timesheet->getEndTime()->diff($timesheet->getStartTime());
            $break    = TimeUtility::getMinutesFromTime($timesheet->getBreak());
            $overTimeMinutes = TimeUtility::getMinutesFromDateInterval($overTime) - $break;
            if($timesheet->getUser()->getStartDate() > $timesheet->getStartTime() || ($timesheet->getUser()->getEndDate() < $timesheet->getEndTime() && $timesheet->getUser()->getEndDate() !== null)){
                $timesheet->setOvertime($overTimeMinutes);
            }
            //todo: verify if Holiday / Bank Holiday / Sickness
            else{
                $hasToWork = OvertimeCalculator::getWorkingHoursForDay($timesheet->getStartTime(), $timesheet->getUser());
                $hasAlreadyWorkedToday = $entityManager->getRepository(Timesheet::class)->getAlreadyWorkedToday($timesheet);
                $hasToWorkMinutes = TimeUtility::getMinutesFromTime($hasAlreadyWorkedToday>0?new \DateTime('00:00:00'):$hasToWork);
                $overTimeMinutes -= $hasToWorkMinutes;
            }
            $timesheet->setOvertime($overTimeMinutes);
            $entityManager->persist($timesheet);
            $entityManager->flush();
            return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timesheet/new.html.twig', [
            'timesheet' => $timesheet,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_timesheet_show', methods: ['GET'])]
    public function show(Timesheet $timesheet): Response
    {
        return $this->render('timesheet/show.html.twig', [
            'timesheet' => $timesheet,
        ]);
    }

    public function edit(Request $request, Timesheet $timesheet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TimesheetType::class, $timesheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $overTime = $timesheet->getEndTime()->diff($timesheet->getStartTime());
            $break    = TimeUtility::getMinutesFromTime($timesheet->getBreak());
            $overTimeMinutes = TimeUtility::getMinutesFromDateInterval($overTime) - $break;
            if($timesheet->getUser()->getStartDate() > $timesheet->getStartTime() || ($timesheet->getUser()->getEndDate() < $timesheet->getEndTime() && $timesheet->getUser()->getEndDate() !== null)){
                $timesheet->setOvertime($overTimeMinutes);
            }
            //todo: verify if Holiday / Bank Holiday / Sickness
            else{
                $hasToWork = OvertimeCalculator::getWorkingHoursForDay($timesheet->getStartTime(), $timesheet->getUser());
                $hasAlreadyWorkedToday = $entityManager->getRepository(Timesheet::class)->getAlreadyWorkedToday($timesheet);
                $hasToWorkMinutes = TimeUtility::getMinutesFromTime($hasAlreadyWorkedToday>0?new \DateTime('00:00:00'):$hasToWork);                
                $overTimeMinutes -= $hasToWorkMinutes;
            }
            $timesheet->setOvertime($overTimeMinutes);
            $entityManager->persist($timesheet);
            $entityManager->flush();

            return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timesheet/edit.html.twig', [
            'timesheet' => $timesheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timesheet_delete', methods: ['POST'])]
    public function delete(Request $request, Timesheet $timesheet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timesheet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($timesheet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
    }



}
