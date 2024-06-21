<?php

namespace App\EventListener;

use App\Entity\Calendar;
use App\Entity\Timesheet;
use App\Manager\HolidayCalculator;
use App\Manager\OvertimeCalculator;
use App\Utility\TimeUtility;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

final class CalendarListener
{
    public function __construct(private EntityManagerInterface $entityManager,private  HolidayCalculator $holidayCalculator, private Security $security)
    {
    }

    public function prePersist(Calendar $calendar, PrePersistEventArgs $event): void
    {
        if($calendar->getUser() == null)
            return;
        $totalDays = $this->holidayCalculator->calculateEffectiveWorkingDays($calendar->getStartDate(),$calendar->getEndDate(),$calendar->getUser());
            
        $minus  = (float)$calendar->getStartMorning()*0.5 + (float)(($calendar->getStartDate() !== $calendar->getEndDate())?$calendar->getEndMorning()*0.5:0);
        $totalDays -= $minus;
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        if($calendar->getTypeOfAbsence()->isHasToBeValidated() && !in_array('ROLE_ADMIN', $roles)){
            $calendar->setState(Calendar::STATE_PENDING);
        }
        $calendar->setAbsenceInWorkingDays($totalDays);   
    }
    public function preUpdate(Calendar $calendar, PreUpdateEventArgs $event): void
    {
        if($calendar->getUser() == null)
        return;
        $totalDays = $this->holidayCalculator->calculateEffectiveWorkingDays($calendar->getStartDate(),$calendar->getEndDate(),$calendar->getUser());
        $minus  = (float)$calendar->getStartMorning()*0.5 + (float)(($calendar->getStartDate() !== $calendar->getEndDate())?$calendar->getEndMorning()*0.5:0);
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $totalDays -= $minus;
        if($calendar->getTypeOfAbsence()->isHasToBeValidated() && !in_array('ROLE_ADMIN', $roles)){
            $calendar->setState(Calendar::STATE_PENDING);
        }
        $calendar->setAbsenceInWorkingDays($totalDays);  
    }
}

