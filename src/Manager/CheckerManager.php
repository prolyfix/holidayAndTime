<?php
namespace App\Manager;

use App\Entity\Calendar;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Entity\Issue;
use Doctrine\ORM\EntityManagerInterface;

class CheckerManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkLastMonth(): bool
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $this->checkUser($user, $startDate, $endDate);
        }
        return true;
    }

    public function checkUser(User $user, \Datetime $startDate, \DateTime $endDate): bool
    {
        $date = clone $startDate;
        while ($date <= $endDate) {
            $hasToWork = false;
            $weekday = $date->format('l');
            foreach ($user->getUserWeekdayProperties() as $userWeekdayProperty) {
                if ($userWeekdayProperty->getWeekday() === $weekday) {
                    if($userWeekdayProperty->getWorkingDay())  
                        $hasToWork = true;
                    break;
                }
            }
            $hasHoliday = false;
            if($this->entityManager->getRepository(Calendar::class)->hasHoliday($user, $date)){
                $hasHoliday = true;
            }
            if(!$hasHoliday && $hasToWork){
                $timesheet = $this->entityManager->getRepository(Timesheet::class)->hasUserWorkedOn( $user,$date);
                if(!$timesheet){
                    $issue = (new Issue())->setUser($user)->setDate(clone($date))->setTitle('Missing timesheet');
                    $this->entityManager->persist($issue);

                }
            }
            $date->modify('+1 day');
        }
        $this->entityManager->flush();
        return true;
    }
}