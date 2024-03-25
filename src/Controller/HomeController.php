<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Calendar;
use App\Manager\HolidayCalculator;
use App\Entity\Timesheet;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_user_me');
    }
    #[Route('/me', name: 'app_user_me', methods: ['GET'])]
    public function me( HolidayCalculator $holidayCalculator,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $groupHolidays = $em->getRepository(Calendar::class)->retrieveHolidaysForGroupForYear($user->getWorkingGroup(), date('Y'));
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
}
