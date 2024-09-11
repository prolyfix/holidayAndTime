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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class CalendarListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private  HolidayCalculator $holidayCalculator, 
        private Security $security,
        private MailerInterface $mailer,
        private ParameterBagInterface $params

    )
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
        if($calendar->getTypeOfAbsence()->isHasToBeValidated() ){
            $calendar->setState(Calendar::STATE_PENDING);
            if($calendar->getUser()->getManager() !== null){
                $email = new TemplatedEmail();
                $email->from(new Address($this->params->get('email_sender'), $this->params->get('email_sender_name')))
                    ->to($calendar->getUser()->getManager()->getEmail())
                    ->subject('Urlaubsanfrage zu genehmigen')
                    ->htmlTemplate('email/urlaubsanfrage.html.twig')
                    ->context([
                        'calendar' => $calendar
                    ]);
                $this->mailer->send($email);            
            }
        }
        $calendar->setAbsenceInWorkingDays($totalDays);   

        if($calendar->getTypeOfAbsence()->isHasToBeValidated() && in_array('ROLE_ADMIN', $roles)){
            $calendar->setState(Calendar::STATE_APPROVED);
        }


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

