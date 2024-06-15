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



    #[Route('/holiday/request/{id}/validate', name: 'holiday_request_approve')]
    public function validate(Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $calendar->setState(Calendar::STATE_APPROVED);
        $entityManager->flush();
        $this->addFlash('success', 'Request approved');
        return $this->redirectToRoute('admin');
    }

    #[Route('/holiday/request/{id}/reject', name: 'holiday_request_reject')]
    public function reject(Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $calendar->setState(Calendar::STATE_REFUSED);
        $entityManager->flush();
        $this->addFlash('success', 'Request rejected');
        return $this->redirectToRoute('admin');
    }


}
