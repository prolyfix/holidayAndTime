<?php

namespace App\EventListener;

use App\Entity\Timesheet;
use App\Manager\OvertimeCalculator;
use App\Utility\TimeUtility;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class TimesheetListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function prePersist(Timesheet $timesheet, PrePersistEventArgs $event): void
    {
        if($timesheet->getOvertime() !== null)
            return;
        $overTime = $timesheet->getEndTime()->diff($timesheet->getStartTime());
        $break    = TimeUtility::getMinutesFromTime($timesheet->getBreak());
        $overTimeMinutes = TimeUtility::getMinutesFromDateInterval($overTime) - $break;
        if($timesheet->getUser()->getStartDate() > $timesheet->getStartTime() || ($timesheet->getUser()->getEndDate() < $timesheet->getEndTime() && $timesheet->getUser()->getEndDate() !== null)){
            $timesheet->setOvertime($overTimeMinutes);
        }
        //todo: verify if Holiday / Bank Holiday / Sickness
        else{
            $hasToWork = OvertimeCalculator::getWorkingHoursForDay($timesheet->getStartTime(), $timesheet->getUser());
            $hasAlreadyWorkedToday = $this->entityManager->getRepository(Timesheet::class)->getAlreadyWorkedToday($timesheet);
            $hasToWorkMinutes = TimeUtility::getMinutesFromTime($hasAlreadyWorkedToday>0?new \DateTime('00:00:00'):$hasToWork);
            $overTimeMinutes -= $hasToWorkMinutes;
        }
        $timesheet->setOvertime($overTimeMinutes);
    }
    public function preUpdate(Timesheet $timesheet, PreUpdateEventArgs $event): void
    {
        if($timesheet->getStartTime() == null)
            return;
        $overTime = $timesheet->getEndTime()->diff($timesheet->getStartTime());
        $break    = TimeUtility::getMinutesFromTime($timesheet->getBreak());
        $overTimeMinutes = TimeUtility::getMinutesFromDateInterval($overTime) - $break;
        if($timesheet->getUser()->getStartDate() > $timesheet->getStartTime() || ($timesheet->getUser()->getEndDate() < $timesheet->getEndTime() && $timesheet->getUser()->getEndDate() !== null)){
            $timesheet->setOvertime($overTimeMinutes);
        }
        //todo: verify if Holiday / Bank Holiday / Sickness
        else{
            $hasToWork = OvertimeCalculator::getWorkingHoursForDay($timesheet->getStartTime(), $timesheet->getUser());
            $hasAlreadyWorkedToday = $this->entityManager->getRepository(Timesheet::class)->getAlreadyWorkedToday($timesheet);
            $hasToWorkMinutes = TimeUtility::getMinutesFromTime($hasAlreadyWorkedToday>0?new \DateTime('00:00:00'):$hasToWork);
            $overTimeMinutes -= $hasToWorkMinutes;
        }
        $timesheet->setOvertime($overTimeMinutes);
    }
}

