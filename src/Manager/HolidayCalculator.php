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
    public static function calculateWorkingDays2(Calendar $calendar): float
    {
        $cloneStartDate = clone $calendar->getStartDate();
        $workingDaysArray = HolidayCalculator::getUserWorkingDays($calendar->getUser());
        $output = 0;
        $interval = new \DateInterval('P1D');
        while($cloneStartDate <= $calendar->getEndDate()){
            if(array_key_exists($cloneStartDate->format('l'), $workingDaysArray) )
            {
                $output += $workingDaysArray[$cloneStartDate->format('l')];
            }
            
            $cloneStartDate->add($interval);
        }
        return $output;
    }

    public static function getUserWorkingDays($user): array{
        $workingDays = $user->getUserWeekdayProperties();
        $workingDaysArray = [];
        foreach($workingDays as $workingDay){
            $workingDaysArray[$workingDay->getWeekday()] = $workingDay->getWorkingDay()??0;
        }
        

        return $workingDaysArray;
    }
    public function handleHalfDay(Calendar $calendar): float
    {
        $workingDaysArray = HolidayCalculator::getUserWorkingDays($calendar->getUser());

        if($calendar->getStartDate() == $calendar->getEndDate()){
            $calendar->setEndMorning(0);
        }
        if($calendar->getStartMorning() == 0 && $calendar->getEndMorning() == 0){
            return 0;
        }
        $output = 0;
        if(array_key_exists($calendar->getStartDate()->format('l'), $workingDaysArray) )
        {
            $output += $calendar->getStartMorning()*0.5;
        }
        if(array_key_exists($calendar->getEndDate()->format('l'), $workingDaysArray) )
        {
            $output += $calendar->getEndMorning()*0.5;
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
    public function calculateEffectiveWorkingDays2(Calendar $calendar, $excludeGroup = false): float
    {
        $totalDays = HolidayCalculator::calculateWorkingDays2( $calendar);            
        $bankHolidays = $this->entityManager->getRepository(Calendar::class)->getBankHolidays( $calendar->getStartDate(),$calendar->getEndDate());
        foreach($bankHolidays as $bankHoliday){
            $totalDays -= HolidayCalculator::calculateWorkingDays2($bankHoliday);            
        }
        $minus = $this->handleHalfDay($calendar);
        $totalDays -= $minus;
        if($excludeGroup){
            return $totalDays;
        }
        $groupHolidays = $this->entityManager->getRepository(Calendar::class)->getGroupHolidays2( $calendar);
        foreach($groupHolidays as $bankHoliday){
            $totalDays -= HolidayCalculator::calculateWorkingDays2($bankHoliday);            
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