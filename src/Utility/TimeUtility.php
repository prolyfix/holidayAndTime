<?php
namespace App\Utility;

use App\Entity\User;

class TimeUtility
{
    public static function getMinutesFromDateInterval(\DateInterval $dateInterval): int
    {
        return $dateInterval->h * 60 + $dateInterval->i;
    }

    public static function getMinutesFromTime(\DateTime $dateTime): int
    {
        return $dateTime->format('H') * 60 + $dateTime->format('i');
    }

    public static function getWorkingHoursForDay(\DateTime $date, User $user): \DateTime
    {
        $weekday = $date->format('N');
        foreach($user->getUserWeekdayProperties() as $userWeekdayProperty){
            if($userWeekdayProperty->getWeekday() === $weekday){
                return $userWeekdayProperty->getWorkingHours();
            }
        }
        return new \DateTime('00:00:00');
    }
    public static function getTimeFromMinutes(int $minutes): \DateTime
    {
        $baseDate = new \DateTime('1970-01-01 00:00:00');

        // Add the given number of minutes to the base date
        $interval = new \DateInterval('PT' . $minutes . 'M');
        $baseDate->add($interval);

        return $baseDate;    }
}