<?php
namespace App\Manager;

use App\Entity\Timesheet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OvertimeCalculator{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getWorkingHoursForDay(\DateTime $date, User $user): \DateTime
    {
        $weekday = $date->format('l');
        dump($weekday);
        foreach($user->getUserWeekdayProperties() as $userWeekdayProperty){
            dump($userWeekdayProperty->getWeekday());
            if($userWeekdayProperty->getWeekday() === $weekday){
                return $userWeekdayProperty->getWorkingHours();
            }
        }
        return new \DateTime('00:00:00');
    
    }

    public function calculateOvertime(Timesheet $timesheet): float
    {
        $lastEntry = $this->entityManager->getRepository(Timesheet::class)->getLastEntry($timesheet);
        

    }

    public function retrieveAwaitedWorkingTime(Timesheet $timesheet): float
    {
        $user = $timesheet->getUser();
        $workingTime = $user->getWorkingTime();
        $break = $timesheet->getBreak();
        $startTime = $timesheet->getStartTime();
        $endTime = $timesheet->getEndTime();
        $totalTime = $endTime->diff($startTime)->format('%H:%I');
        $totalTime = explode(':', $totalTime);
        $totalTime = $totalTime[0] + $totalTime[1] / 60;
        $totalTime -= $break;
        $awaitedWorkingTime = $workingTime - $totalTime;
        return $awaitedWorkingTime;
    }
}