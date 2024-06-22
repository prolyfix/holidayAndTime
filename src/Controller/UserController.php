<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Entity\UserProperty;
use App\Entity\UserWeekdayProperty;
use App\Form\datatables\DatatablesUserType;
use App\Form\UserType;
use App\Manager\HolidayCalculator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $tableParams = [ 
            'tableId' => 'history', 
            'targetEntity' => User::class, 
            'columns' => [ 
                ['name' =>  'email',    
                  'label' => 'email',
                ],  
                ['name' =>  'manager.email',    
                'label' => 'manager',
                ], 
                ['name' =>  '_action',    
                'label' => 'actions',
                ],                 
            ], 
            'params' => ['order' => [[1,'desc']]], 
            'tableTitle' => 'users' ,
            
        ];
        return $this->render('user/index.html.twig', [
            'tableParams' => $tableParams,
            'filter'    => User::class,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $timestamp = strtotime('next Monday');
        for ($i = 0; $i < 7; $i++) {
            $userWeekdayProperty = new UserWeekdayProperty();
            $userWeekdayProperty->setUser($user);
            $userWeekdayProperty->setWeekday(strftime('%A', $timestamp));
            $timestamp = strtotime('+1 day', $timestamp);
            $user->addUserWeekdayProperty($userWeekdayProperty);
        }

        $userProperty = new UserProperty();
        $userProperty->setUser($user);
        $userProperty->setHolidayPerYear(25); // default value
        $user->addUserProperty($userProperty);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword('password');
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, HolidayCalculator $holidayCalculator, EntityManagerInterface $em): Response
    {
        $groupHolidays = $em->getRepository(Calendar::class)->retrieveHolidaysForGroupForYear($user->getWorkingGroup(), date('Y'));
        $groupHolidaysCount = 0;
        foreach($groupHolidays as $holiday){
            $groupHolidaysCount += $holidayCalculator->calculateEffectiveWorkingDays($holiday->getStartDate(),$holiday->getEndDate(),$user, true);
        }
        $groupHolidays = $em->getRepository(Calendar::class)->retrieveHolidaysForFirmForYear( date('Y'));
        $groupHolidaysCount = 0;
        foreach($groupHolidays as $holiday){
            $groupHolidaysCount += $holidayCalculator->calculateEffectiveWorkingDays($holiday->getStartDate(),$holiday->getEndDate(),$user, true);
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'holidayForYear'        => $holidayCalculator->calculateHolidayForYear($user, date('Y')),
            'pendingForYear'        => $em->getRepository(Calendar::class)->calculatePendingForYear($user, date('Y')),
            'holidayTakenForYear'   => $em->getRepository(Calendar::class)->retrieveHolidayForYear($user, date('Y')),
            'overtime'       => $em->getRepository(Timesheet::class)->retrieveOvertimeForUser($user),
            'groupHolidays' => $groupHolidaysCount,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }




}
