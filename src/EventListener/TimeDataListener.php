<?php

namespace App\EventListener;

use App\Entity\Calendar;
use App\Entity\Commentable;
use App\Entity\TimeData;
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

final class TimeDataListener
{
    public function __construct(private EntityManagerInterface $entityManager,private  HolidayCalculator $holidayCalculator, private Security $security)
    {
    }

    public function prePersist(TimeData $commentable, PrePersistEventArgs $event): void
    {
        $user = $this->security->getUser();
        $commentable->setCreatedBy($user);
    }
}

