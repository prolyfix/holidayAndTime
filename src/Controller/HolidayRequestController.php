<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Entity\User;
use App\Manager\HolidayCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class HolidayRequestController extends AbstractController
{
    #[Route('/holiday/request', name: 'app_holiday_request')]
    public function index(EntityManagerInterface $em): Response
    {
        $myOpenRequests = $this->getUser()->getCalendars()->filter(function(Calendar $calendar){
            return $calendar->getState() === Calendar::STATE_PENDING;
        });
        $myTeam = $this->getUser()->getUsers();
        $openRequestOfMyTeam = $em->getRepository(Calendar::class)->findOpenRequests($myTeam);

        return $this->render('holiday_request/index.html.twig', [
            'my_open_request' => $myOpenRequests,
            'holidayRequests' => $openRequestOfMyTeam,
        ]);
    }

    #[Route('/holiday/request/new', name: 'holiday_request_new')]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer, HolidayCalculator $holidayCalculator): Response
    {
        $calendar = new Calendar();
        $calendar->setUser($this->getUser());

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->remove('user');
        $form->remove('workingGroup');
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $hasAlreadyHolidayBookedDuringPeriod = $em->getRepository(Calendar::class)->hasAlreadyHolidayBookedDuringPeriod($calendar);
            if($hasAlreadyHolidayBookedDuringPeriod){
                $this->addFlash('danger', 'You already have booked holiday during this period');
                return $this->redirectToRoute('app_calendar_index');
            }
            $totalDays = $holidayCalculator->calculateEffectiveWorkingDays($calendar->getStartDate(),$calendar->getEndDate(),$calendar->getUser());
            if($calendar->getTypeOfAbsence()->isHasToBeValidated()){
                $calendar->setState(Calendar::STATE_PENDING);
            }
            $calendar->setAbsenceInWorkingDays($totalDays);

            $em->persist($calendar);
            $em->flush();
            if($this->getUser()->getManager() !== null){
                $email = (new TemplatedEmail())
                            ->from('personnal@frauengesundheit-am-see.de')
                            ->to($this->getUser()->getManager()->getEmail())
                            ->subject('Urlaubsantrag')
                            ->htmlTemplate('email/holiday_request.html.twig')
                            ->context([
                                'calendar' => $calendar,
                            ]);
                $mailer->send($email);
            }


            return $this->redirectToRoute('app_calendar_index');
        }
        
        return $this->render('calendar/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/holiday/request/{id}/validate', name: 'holiday_request_approve')]
    public function validate(Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $calendar->setState(Calendar::STATE_APPROVED);
        $entityManager->flush();
        return $this->redirectToRoute('app_calendar_index');
    }

    #[Route('/holiday/request/{id}/reject', name: 'holiday_request_reject')]
    public function reject(Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $calendar->setState(Calendar::STATE_REFUSED);
        $entityManager->flush();
        return $this->redirectToRoute('app_calendar_index');
    }


}
