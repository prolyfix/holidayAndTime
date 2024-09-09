<?php
namespace App\Manager;

use App\Entity\Calendar;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class HolidayCalculator{

    const DAYS_IN_MONTHS = [
        1 => 31,
        2 => 28,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31,
    ];

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function calculateWorkingDays(\DateTime $startDate, \DateTime $endDate, User $user): float
    {
        $cloneStartDate = clone $startDate;
        $workingDays = $user->getRightUserWeekdayProperties($cloneStartDate);
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
        $workingDaysArray = HolidayCalculator::getUserWorkingDays($calendar->getUser(), $cloneStartDate);
        $output = 0;
        $interval = new \DateInterval('P1D');
        while($cloneStartDate <= $calendar->getEndDate()){
            if(array_key_exists($cloneStartDate->format('l'), $workingDaysArray) && $calendar->getUser()->getStartDate() <= $cloneStartDate)
            {
                $output += $workingDaysArray[$cloneStartDate->format('l')];
            }
            
            $cloneStartDate->add($interval);
        }
        return $output;
    }

    public static function getUserWorkingDays($user, $date): array{
        $workingDays = $user->getRightUserWeekdayProperties($date);
        $workingDaysArray = [];
        foreach($workingDays as $workingDay){
            $workingDaysArray[$workingDay->getWeekday()] = $workingDay->getWorkingDay()??0;
        }
        

        return $workingDaysArray;
    }
    public function handleHalfDay(Calendar $calendar): float
    {
        $workingDaysArray = HolidayCalculator::getUserWorkingDays($calendar->getUser(),$calendar->getStartDate());

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


    public function calculateEffectiveWorkingDays(\DateTime $startDate, \DateTime $endDate, User $user, $excludeGroup = false, $ref = null): float
    {
        $totalDays = HolidayCalculator::calculateWorkingDays( $startDate,$endDate,$user);            
        $bankHolidays = $this->entityManager->getRepository(Calendar::class)->getBankHolidays( $startDate,$endDate);
        foreach($bankHolidays as $bankHoliday){
            if($ref !== $bankHoliday->getId())
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

    public function calculateHolidayFromRequest($data)
    {
        $urlaubsAnsprüche = $data['restUrlaub']??0;
        foreach($data['periode'] as $period)
        {
            $startDate = $period['startDate'];
            $endDate = $period['endDate'];
            $numberHolidayForYear = $period['numberHolidayForYear'];
            $actualWorkingDays = $period['actualWorkingDays'];
            if($startDate->format('Y') < $data['yearToReview']){
                $startDate->setDate($data['yearToReview'],1,1);
            }
            if($startDate>=$endDate || $startDate->format('Y') > $data['yearToReview']){
                continue;
            }
            if($endDate->format('Y') > $data['yearToReview']){
                $endDate->setDate($data['yearToReview'],12,31);
            }
            $numberOfMonths = $startDate->diff($endDate)->m + 1;

            if((float)$startDate->format('d')!== 1){
                $diff = $this->calculateSubMonth($startDate, 1);
                $numberOfMonths += -1 + $diff;
            }
            if($endDate->format('d')!== HolidayCalculator::DAYS_IN_MONTHS[(float)$endDate->format('m')]){
                $diff = $this->calculateSubMonth($endDate);
                $numberOfMonths += - $diff;

            }


            $urlaubsAnsprüche += $numberHolidayForYear * $numberOfMonths / 12 * $actualWorkingDays / 5;
        }
        return $urlaubsAnsprüche;
    }

    public function calculateSubMonth(\DateTime $startDate, float $offset = 0): float
    {
        $output = 0;
        $numberOfDaysInMonth = HolidayCalculator::DAYS_IN_MONTHS[(float)$startDate->format('m')];
        $output += $numberOfDaysInMonth - $startDate->format('d') + $offset ;
        return $output/$numberOfDaysInMonth;
    }

}