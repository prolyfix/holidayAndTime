<?php

namespace App\EventListener;

use App\Entity\Timesheet;
use App\Entity\UserWeekdayProperty;
use App\Manager\OvertimeCalculator;
use App\Utility\TimeUtility;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class UserWeekdayPropertyListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function prePersist(UserWeekdayProperty $userWeekdayProperty, PrePersistEventArgs $event): void
    {
        if($userWeekdayProperty->getUserSchedule()->getUser()->getCompany()== null)
            return;
        //TODO: voir pour utiliser la propriété de l'entreprises
        //$threshold = $userWeekdayProperty->getUserSchedule()->getUser()->getCompany()->getConfiguration('thresholdHalfDay');
        $threshold = null;
        if($threshold === null){
            return;
        }

        $timeThreshold = TimeUtility::getTimeFromMinutes($threshold->getValue()*60);
        if($userWeekdayProperty->getWorkingHours() ==TimeUtility::getTimeFromMinutes(0) || $userWeekdayProperty->getWorkingHours() == null){
            $userWeekdayProperty->setWorkingDay(0);
            return;
        }
        
        if($userWeekdayProperty->getWorkingHours() > $timeThreshold){
            $userWeekdayProperty->setWorkingDay(1);
            return;
        }
        $userWeekdayProperty->setWorkingDay(0.5);
        return;
    }
    public function preUpdate(UserWeekdayProperty $userWeekdayProperty, PreUpdateEventArgs $event): void
    {
        $threshold = $userWeekdayProperty->getUserSchedule()->getUser()->getCompany()->getConfiguration('thresholdHalfDay');
        if($threshold === null){
            return;
        }

        $timeThreshold = TimeUtility::getTimeFromMinutes($threshold->getValue()*60);
        if($userWeekdayProperty->getWorkingHours() ==TimeUtility::getTimeFromMinutes(0) || $userWeekdayProperty->getWorkingHours() == null){
            $userWeekdayProperty->setWorkingDay(0);
            return;
        }
        
        if($userWeekdayProperty->getWorkingHours() > $timeThreshold){
            $userWeekdayProperty->setWorkingDay(1);
            return;
        }
        $userWeekdayProperty->setWorkingDay(0.5);
        return;
    }
}

