<?php

namespace App\Tests\Utility;

use App\Utility\TimeUtility;
use App\Entity\User;
use App\Entity\UserWeekdayProperty;
use PHPUnit\Framework\TestCase;

class TimeUtilityTest extends TestCase
{
    public function testGetMinutesFromDateInterval()
    {
        $dateInterval = new \DateInterval('PT1H30M');
        $minutes = TimeUtility::getMinutesFromDateInterval($dateInterval);
        $this->assertEquals(90, $minutes);
    }

    public function testGetMinutesFromTime()
    {
        $dateTime = new \DateTime('10:30');
        $minutes = TimeUtility::getMinutesFromTime($dateTime);
        $this->assertEquals(630, $minutes);
    }

    public function testGetWorkingHoursForDay()
    {
        $user = $this->createMock(User::class);
        $userWeekdayProperty = $this->createMock(UserWeekdayProperty::class);
        $userWeekdayProperty->method('getWeekday')->willReturn(1);
        $userWeekdayProperty->method('getWorkingHours')->willReturn(new \DateTime('08:00:00'));

        $user->method('getUserWeekdayProperties')->willReturn([$userWeekdayProperty]);

        $date = new \DateTime('2023-10-02'); // Monday
        $workingHours = TimeUtility::getWorkingHoursForDay($date, $user);
        $this->assertEquals('08:00:00', $workingHours->format('H:i:s'));

        // Test for a day with no working hours set
        $date = new \DateTime('2023-10-03'); // Tuesday
        $workingHours = TimeUtility::getWorkingHoursForDay($date, $user);
        $this->assertEquals('00:00:00', $workingHours->format('H:i:s'));
    }
}