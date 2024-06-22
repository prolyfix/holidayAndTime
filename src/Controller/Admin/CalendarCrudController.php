<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\User;
use App\Form\CalendarType;
use App\Manager\HolidayCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('startDate'),
            BooleanField::new('startMorning'),
            DateField::new('endDate'),
            BooleanField::new('endMorning'),
            AssociationField::new('user'),
            AssociationField::new('workingGroup'),
            BooleanField::new('isAll'),
            AssociationField::new('typeOfAbsence'),
            ChoiceField::new('state')->setChoices([
                'Pending' => Calendar::STATE_PENDING,
                'Approved' => Calendar::STATE_APPROVED,
                'Refused' => Calendar::STATE_REFUSED,
            ]),
            NumberField::new('absenceInWorkingDays')->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user')
            ->add('startDate')
            ->add('endDate')
            ->add('typeOfAbsence')
            
            ->add('workingGroup')
        ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $viewYear = Action::new('viewYear', 'View Year', 'fa fa-calendar')
                            ->linkToCrudAction('viewYear')
                            ->createAsGlobalAction();
        return $actions->add(Crud::PAGE_INDEX, $viewYear);
    }

    #[Route('/admin/holiday/request', name: 'admin_holiday_request')]
        public function viewRequest(EntityManagerInterface $em, Request $request): Response
    {
        $userId = $request->get('entityId');
        $user = $userId != null ? $em->getRepository(User::class)->findOneById($userId):$this->getUser();
        $myOpenRequests = $this->getUser()->getCalendars()->filter(function(Calendar $calendar){
            return $calendar->getState() === Calendar::STATE_PENDING;
        });
        $myTeam = $this->getUser()->getUsers();
        $openRequestOfMyTeam = $em->getRepository(Calendar::class)->findOpenRequests($myTeam);

        return $this->render('holiday_request/index.html.twig', [
            'my_open_request' => $myOpenRequests,
            'holidayRequests' => $openRequestOfMyTeam,
            'user'            => $user
        ]);
    }

    #[Route('/admin/holiday/request/edit', name: 'admin_holiday_request_edit')]
    public function editHolidayRequest(Request $request, EntityManagerInterface $em, MailerInterface $mailer, HolidayCalculator $holidayCalculator): Response
    {
        $calendarId = $request->get('entityId');
        $calendar = $em->getRepository(Calendar::class)->find($calendarId);
        $user = $calendar->getUser();
        $this->denyAccessUnlessGranted('POST_EDIT', $calendar, 'You are not allowed to create a holiday request for this user');
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->remove('user');
        $form->remove('workingGroup');
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){

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


            return $this->redirectToRoute('admin',[
                'crudAction' => 'show',
                'entityId' => $user->getId(),
                'crudController' => 'UserCrudController'
            ]);
        }
        
        return $this->render('calendar/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    public function viewYear(EntityManagerInterface $em, Request $request): Response
    {
        $year = $request->get('year')??date('Y');
        $output = [];
        $dateStart = new \DateTime($year.'-01-01');
        $dateEnd = new \DateTime($year.'-12-31');
        $interval = new \DateInterval('P1M');
        $users = $em->getRepository(User::class)->findBy([],['workingGroup'=>'ASC']);
        $outputUser = [];
        $outputBankHolidays = [];
        while($dateStart <= $dateEnd){
            $output[ $dateStart->format('Y-m-d')]['date'] = clone($dateStart);
            $dateStart->add($interval);
        }
        $calendars = $em->getRepository(Calendar::class)->getCalendarsForYear($year);
        foreach($calendars as $calendar){
            if(!$calendar->getTypeOfAbsence()->isIsWorkingDay() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $calendar->getUser() == $this->getUser() || $calendar->getWorkingGroup() == $this->getUser()->getWorkingGroup()) )
            {
                $cloneStartDate = clone($calendar->getStartDate());
                $di = new \DateInterval('P1D');
                while($cloneStartDate <= $calendar->getEndDate()){
                    if($calendar->getUser() !== null){
                        $outputUser[$calendar->getUser()->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                    }
                    elseif($calendar->getWorkingGroup()!==null){
                        foreach($calendar->getWorkingGroup()->getUsers() as $user){
                            $outputUser[$user->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                        }
                    }else{
                        foreach($users as $user){
                            $outputUser[$user->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                        }
                    }
                    $cloneStartDate->add($di);
                }
                
            }
        }
        $outputWorkingDays = [];
        foreach($calendars as $calendar){
            if($calendar->getTypeOfAbsence()->isIsWorkingDay() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $calendar->getUser() == $this->getUser() || $calendar->getWorkingGroup() == $this->getUser()->getWorkingGroup() ))
            {
                $cloneStartDate = clone($calendar->getStartDate());
                $di = new \DateInterval('P1D');
                while($cloneStartDate <= $calendar->getEndDate()){
                    if($calendar->getUser() !== null){
                        $outputWorkingDays[$calendar->getUser()->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                    }
                    elseif($calendar->getWorkingGroup()!==null){
                        foreach($calendar->getWorkingGroup()->getUsers() as $user){
                            $outputWorkingDays[$user->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                        }
                    }else{
                        foreach($users as $user){
                            $outputWorkingDays[$user->getId()][$cloneStartDate->format('Y-m-d')][] = $calendar;
                        }
                    }
                    $cloneStartDate->add($di);
                }
                
            }
        }
        $bankHolidays = $em->getRepository(Calendar::class)->getBankHolidays(new \DateTime($year.'-01-01'), new \DateTime($year.'-12-31'));

        foreach($bankHolidays as $bankHoliday){
            $outputBankHolidays[$bankHoliday->getStartDate()->format('Y-m-d')][] = $bankHoliday;
        }

        $users = in_array('ROLE_ADMIN', $this->getUser()->getRoles())?$em->getRepository(User::class)->findBy([],['workingGroup'=>'ASC']):[$this->getUser()];
        $groupCount = [];
        foreach($users as $user){
            if($user->isIsDeactivated()) continue;
            if($user->getWorkingGroup() == null){
                $groupCount['other'][] = $user;
            }
            elseif(!array_key_exists($user->getWorkingGroup()->getName(), $groupCount)){
                $groupCount[$user->getWorkingGroup()->getName()] = array($user);
            }else{
                $groupCount[$user->getWorkingGroup()->getName()][] = $user;
            }
            
        }
        return $this->render('calendar/yearView.html.twig', [
            'output' => $output,
            'users' => $users,
            'groupCount' => $groupCount,
            'outputUser' => $outputUser,
            'outputBankHolidays' => $outputBankHolidays,
            'outputWorkingDays' => $outputWorkingDays
        ]);
    }

    #[Route('/holiday/request/new', name: 'holiday_request_new')]
    public function newHolidayRequest(Request $request, EntityManagerInterface $em, MailerInterface $mailer, HolidayCalculator $holidayCalculator): Response
    {
        $userId = $request->get('entityId');
        $user = $em->getRepository(User::class)->find($userId);
        $calendar = new Calendar();
        $calendar->setUser($user);
        $dateS = $request->get('date');
        if($dateS !== null){
            $date = \DateTime::createFromFormat('d-m-Y',$dateS);
            $calendar->setStartDate($date);
            $calendar->setEndDate($date);
        }

        $this->denyAccessUnlessGranted('POST_EDIT', $calendar, 'You are not allowed to create a holiday request for this user');


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


            return $this->redirectToRoute('admin',[
                'crudAction' => 'show',
                'entityId' => $calendar->getUser()->getId(),
                'crudController' => 'UserCrudController'
            ]);
        }
        
        return $this->render('calendar/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser();
        $query = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if($user->hasRole(User::ROLE_ADMIN))
        {
            return $query;
        }
        if(count($user->getUsers()) > 0)
        {
            return $query->andWhere('entity.user IN (:users)')
            ->setParameter('users', $user->getUsers())
            ->orWhere('entity.user = :user')   
            ->setParameter('user', $user); 
        }

        if(count($user->getUsers()) == 0){
            return $query->andWhere('entity.user = :user')
            ->setParameter('user', $user);
        }
    }
}
