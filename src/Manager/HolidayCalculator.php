<?php
namespace App\Manager;

use App\Entity\Calendar;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class HolidayCalculator{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function calculateWorkingDays(\DateTime $startDate, \DateTime $endDate, User $user): float
    {
        $cloneStartDate = clone $startDate;
        $workingDays = $user->getUserWeekdayProperties();
        $workingDaysArray = [];
        foreach($workingDays as $workingDay){
            $workingDaysArray[$workingDay->getWeekday()] = $workingDay->getWorkingDay()??0;
        }
        $output = 0;
        $interval = new \DateInterval('P1D');
        while($cloneStartDate <= $endDate){
            if(array_key_exists($cloneStartDate->format('l'), $workingDaysArray) )
            {
                $output += $workingDaysArray[$cloneStartDate->format('l')];
            }
            
            $cloneStartDate->add($interval);
        }
        return $output;
    }

    public function calculateEffectiveWorkingDays(\DateTime $startDate, \DateTime $endDate, User $user, $excludeGroup = false): float
    {
        $totalDays = HolidayCalculator::calculateWorkingDays( $startDate,$endDate,$user);            
        $bankHolidays = $this->entityManager->getRepository(Calendar::class)->getBankHolidays( $startDate,$endDate);
        foreach($bankHolidays as $bankHoliday){
            $totalDays -= HolidayCalculator::calculateWorkingDays($bankHoliday->getStartDate(),$bankHoliday->getEndDate(),$user);            
        }
        if($excludeGroup){
            return $totalDays;
        }
        $groupHolidays = $this->entityManager->getRepository(Calendar::class)->getGroupHolidays( $startDate,$endDate,$user->getWorkingGroup());
        foreach($groupHolidays as $bankHoliday){
            $totalDays -= HolidayCalculator::calculateWorkingDays($bankHoliday->getStartDate(),$bankHoliday->getEndDate(),$user);            
        }
         return $totalDays;
    }


    public function calculateHolidayForYear(User $user, int $year): float
    {
        $startDate = $user->getStartDate();
        $startYear = new \DateTime($year . '-01-01');
        $startToConsider = $startYear;
        if ($startDate > $startYear) {
            $startToConsider = $startDate;
        }
        $endYear = new \DateTime($year . '-12-31');
        $endToConsider = $endYear;
        $endDay = $user->getEndDate();
        if ($endDay && $endDay < $endYear) {
            $endToConsider = $endDay;
        }
        $totalDays = $startToConsider->diff($endToConsider)->days;
        $totalYearDays = $startYear->diff($endYear)->days;
        $holidayPerYear = isset($user->getUserProperties()[0])?$user->getUserProperties()[0]->getHolidayPerYear():0;
        return $holidayPerYear * $totalDays / $totalYearDays;

    }

}