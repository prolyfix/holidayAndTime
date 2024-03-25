<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\User;
use App\Form\CalendarType;
use App\Manager\HolidayCalculator;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Manager\OvertimeCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Choice;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $tableParams = [ 
            'tableId' => 'history', 
            'targetEntity' => Calendar::class, 
            'columns' => [ 
                ['name' =>  'startDate',    
                  'label' => 'startDate',
                ],  
                ['name' =>  'endDate',    
                'label' => 'endDate',
                ], 
                ['name' =>  'user.email',    
                'label' => 'user',
                ],       
                ['name' =>  'absenceInWorkingDays',    
                'label' => 'inWorkingDays',
                ],  
       
                ['name' =>  'state',    
                'label' => 'state',
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
                'url' =>  $this->generateUrl('app_calendar_new')
            ]
        ];
        return $this->render('gestion.html.twig', [
            'tableParams' => $tableParams,
            'filter'    => Calendar::class,
            'title' => 'Calendar',
            'buttons' => $buttons   
        ]);
    }

    #[Route('/jahr/{year}', name: 'app_calendar_overview_jahr', methods: ['GET'])]
    public function jahrView(int $year, EntityManagerInterface $em): Response
    {
        $output = [];
        $dateStart = new \DateTime($year.'-01-01');
        $dateEnd = new \DateTime($year.'-12-31');
        $interval = new \DateInterval('P1M');
        $outputUser = [];
        $outputBankHolidays = [];
        while($dateStart <= $dateEnd){
            $output[ $dateStart->format('Y-m-d')]['date'] = clone($dateStart);
            $dateStart->add($interval);
        }
        $calendars = $em->getRepository(Calendar::class)->getCalendarsForYear($year);
        foreach($calendars as $calendar){
            if(in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $calendar->getUser() == $this->getUser() || $calendar->getWorkingGroup() == $this->getUser()->getWorkingGroup() )
            {
                $cloneStartDate = clone($calendar->getStartDate());
                $di = new \DateInterval('P1D');
                while($cloneStartDate <= $calendar->getEndDate()){
                    if($calendar->getUser() !== null){
                        $outputUser[$calendar->getUser()->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                    }
                    else{
                        foreach($calendar->getWorkingGroup()->getUsers() as $user){
                            $outputUser[$user->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                        }
                    }
                    $cloneStartDate->add($di);
                }
                
            }
        }
        $bankHolidays = $em->getRepository(Calendar::class)->getBankHolidays(new \DateTime($year.'-01-01'), new \DateTime($year.'-12-31'));

        foreach($bankHolidays as $bankHoliday){
            $outputBankHolidays[$bankHoliday->getStartDate()->format('Y-m-d')][] = $bankHoliday;
        }

        $users = in_array('ROLE_ADMIN', $this->getUser()->getRoles())?$em->getRepository(User::class)->findBy([],['workingGroup'=>'ASC']):[$this->getUser()];
        $groupCount = [];
        foreach($users as $user){
            if($user->getWorkingGroup() == null){
                $groupCount['other'][] = $user;
            }
            elseif(!array_key_exists($user->getWorkingGroup()->getName(), $groupCount)){
                $groupCount[$user->getWorkingGroup()->getName()] = array($user);
            }else{
                $groupCount[$user->getWorkingGroup()->getName()][] = $user;
            }
            
        }
        foreach($users as $user) 
        return $this->render('calendar/yearView.html.twig', [
            'output' => $output,
            'users' => $users,
            'groupCount' => $groupCount,
            'outputUser' => $outputUser,
            'outputBankHolidays' => $outputBankHolidays
        ]);
    }


    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, HolidayCalculator $holidayCalculator): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $roles = $this->getUser()->getRoles();
        if(in_array('ROLE_ADMIN', $roles)){
            $form->add('state',ChoiceType::class, [
                'choices'  => [
                    'pending' => 'pending',
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                ],
                'attr' => ['class' => 'form-control']
            ]);
        }else{
            $form->remove('user');
            $form->remove('group');
            $calendar->setState('pending');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAlreadyHolidayBookedDuringPeriod = $entityManager->getRepository(Calendar::class)->hasAlreadyHolidayBookedDuringPeriod($calendar);
            if($hasAlreadyHolidayBookedDuringPeriod){
                $this->addFlash('danger', 'You already have booked holiday during this period');
                return $this->redirectToRoute('app_calendar_index');
            }
            if($calendar->getUser() !== null){
                $totalDays = $holidayCalculator->calculateEffectiveWorkingDays($calendar->getStartDate(),$calendar->getEndDate(),$calendar->getUser());
            
                $minus  = (float)$calendar->getStartMorning() + (float)(($calendar->getStartDate() !== $calendar->getEndDate())?$calendar->getEndMorning():0);
                $totalDays -= $minus;
                if($calendar->getTypeOfAbsence()->isHasToBeValidated() && !in_array('ROLE_ADMIN', $roles)){
                    $calendar->setState(Calendar::STATE_PENDING);
                }
                $calendar->setAbsenceInWorkingDays($totalDays);    
            }
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager, HolidayCalculator $holidayCalculator): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalDays = $holidayCalculator->calculateEffectiveWorkingDays($calendar->getStartDate(),$calendar->getEndDate(),$calendar->getUser());
            if($calendar->getTypeOfAbsence()->isHasToBeValidated()){
                $calendar->setState(Calendar::STATE_PENDING);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

}
