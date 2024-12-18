<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use Prolyfix\TimesheetBundle\Entity\Timesheet;
use App\Entity\User;
use App\Form\UserPropertyType;
use App\Form\UserScheduleType;
use App\Manager\HolidayCalculator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private MailerInterface $mailer, private EntityManagerInterface $em, private Security $security, private ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->security = $security;
        $this->mailer = $mailer;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/edit', 'admin/user/new.html.twig')
            ->overrideTemplate('crud/new', 'admin/user/new.html.twig')
        ;
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('email')
            ->add('manager')
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $company = $this->getUser()->getCompany();
        $entityInstance->setCompany($company);
        $entityInstance->setPassword(uniqid());
        parent::persistEntity($entityManager, $entityInstance);
        $email = new TemplatedEmail();
        $email->from(new Address($this->params->get('email_sender'), $this->params->get('email_sender_name')))
            ->to($entityInstance->getEmail())
            ->subject('Registrierung bei der Verwaltung von Urlaub und Überstunden von '.$entityInstance->getCompany()->getName())
            ->htmlTemplate('email/registration.html.twig')
            ->context([
                'user' => $entityInstance
            ]);
            $this->mailer->send($email);
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewYear = Action::new('show', 'show', 'fa fa-user')
        ->linkToCrudAction('show');
        return $actions
            ->add('index', $viewYear);
    }


    public function configureFields(string $pageName): iterable
    {
        $user = $this->security->getUser();
        return [
            TextField::new('name'),
            TextField::new('email'),
            AssociationField::new('manager')->setFormTypeOption('query_builder', function ($entity) use ($user) {
                return $entity->createQueryBuilder('m')
                    ->andWhere('m.company = :company')
                    ->setParameter('company', $user->getCompany());
            }),
            AssociationField::new('workingGroup'),
            DateField::new('startDate'),
            DateField::new('endDate')->hideOnIndex(),
            CollectionField::new('userSchedules')->hideOnIndex()->setEntryType(UserScheduleType::class),
            CollectionField::new('userProperties')->setEntryType(UserPropertyType::class),
            BooleanField::new('hasTimesheet'),
            BooleanField::new('isDeactivated') ,
            BooleanField::new('emailInteraction'),
            ImageField::new('avatarFilename')->setUploadDir('public/uploads/avatar')->setBasePath('uploads/logo')->hideOnIndex(),
            ColorField::new('color'),


        ];
    }
    



    public function monthView(Request $request): Response
    {   
        $em = $this->em;
        $year = $request->get('year') ?? date('Y');
        $month = $request->get('month') ?? date('m');
        if($request->get('userId')){
            $user = $em->getRepository(User::class)->find($request->get('userId'));
        }else{
            $user = $this->getContext()->getEntity()->getInstance();
        }

        $monthBegin = new \DateTime($year.'-'.$month.'-01');
        $monthEnd = new \DateTime($year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, $month, $year));
        $timesheets = $em->getRepository(Timesheet::class)->retrieveOvertimeForUserForPeriod($user, $monthBegin, $monthEnd);
        $holidays   = $em->getRepository(Calendar::class)->retrieveHolidaysForUser($user, $monthBegin, $monthEnd);
        $groupHolidays   = $em->getRepository(Calendar::class)->retrieveHolidaysForGroup($user->getWorkingGroup(), $monthBegin, $monthEnd);
        $firmHolidays = $em->getRepository(Calendar::class)->retrieveAllHolidaysForFirmForYear($year);
        $bankHolidays = $em->getRepository(Calendar::class)->retrieveBankHolidaysForYear($year);
        $workingDays = array();
        //foreach($user->getUserWeekdayProperties() as $weekday){
        //    if($weekday->getWorkingDay())
        //        $workingDays[] = $weekday->getWeekday();
        
        //}
        return $this->render('calendar/monthView.html.twig', [
            'user' => $user,
            'month' => $month,
            'monthName' => date('F', strtotime($year.'-'.$month.'-01')),
            'year' => $year,
            'monthBegin' => $monthBegin,
            'timesheet' => $timesheets,
            'holidays' => $holidays,
            'groupHolidays' => $groupHolidays,
            'bankHolidays' => $bankHolidays,
            'workingDays' => $workingDays,
            'companyHolidays' => $firmHolidays
        ]);
    }
    public function createIndexQueryBuilder($searchDto,  $entityDto, $fields, $filters): QueryBuilder
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $queryBuilder = parent::createIndexQueryBuilder( $searchDto,  $entityDto,  $fields,  $filters);
        
        if($user->hasRole('SUPER_ADMIN')){
            return $queryBuilder;
        }

        $queryBuilder->andwhere('entity.company = :company')
            ->setParameter('company', $user->getCompany());

        if ($user->hasRole('ROLE_ADMIN')) {
            return $queryBuilder;
        }

        if($user->hasRole('ROLE_MANAGER')){
            $queryBuilder->andWhere('entity.workingGroup = :manager')
                ->setParameter('manager', $user->getWorkingGroup());
            return $queryBuilder;
        }
        $queryBuilder
            ->andWhere('entity.id = :userId')
            ->setParameter('userId', $user->getId());

        return $queryBuilder;
    }

    public function show( HolidayCalculator $holidayCalculator, EntityManagerInterface $em): Response
    {

        $year = date('Y');
        $nextYear = date('Y') + 1;
        $table = [];
        //$table[$year] = ['anspruch' => 0, 'groupHoliday'=>0, 'singleHoliday'=>0, 'openHoliday'=>0, 'restHoliday'=>0];
        $table[$year + 1] = ['anspruch' => 0, 'groupHoliday'=>0, 'singleHoliday'=>0, 'openHoliday'=>0, 'restHoliday'=>0];
        $user = $this->getContext()->getEntity()->getInstance();
        foreach($user->getUserProperties() as $property){
            if(isset($table[$property->getYear()]))
                $table[$property->getYear()]['anspruch'] = $property->getHolidayPerYear();
        }
        $groupHolidays = $em->getRepository(Calendar::class)->retrieveHolidaysForGroupForYear($user->getWorkingGroup(),$year);
        $groupHolidaysCount = 0;
        foreach($groupHolidays as $holiday){
            $holiday->setUser($user);
            $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
            $groupHolidaysCount += $days;
            $holiday->setAbsenceInWorkingDays($days);
            //$table[$year]['groupHoliday'] += $days;
        }
        $groupHolidays2 = $em->getRepository(Calendar::class)->retrieveHolidaysForFirmForYear( $year);
        foreach($groupHolidays2 as $holiday){
            $holiday->setUser($user);
            $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
            $groupHolidaysCount += $days;
            $holiday->setAbsenceInWorkingDays($days);
            //$table[$year]['groupHoliday'] += $days;
        }
        $userHolidays = $em->getRepository(Calendar::class)->retrieveListHolidayForYear($user, $year);
        foreach($userHolidays as $holiday){
            if($holiday->getTypeOfAbsence()->isIsHoliday())
                $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
            //$table[$year]['singleHoliday'] += $days;
        }
        $userHolidays = $em->getRepository(Calendar::class)->retrieveListHolidayForYear($user, $nextYear);
        foreach($userHolidays as $holiday){
            if($holiday->getTypeOfAbsence()->isIsHoliday()){
                $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
                $table[$nextYear]['singleHoliday'] += $days;
            }
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'holidayForYear'        => $holidayCalculator->calculateHolidayForYear($user, date('Y')),
            'pendingForYear'        => $em->getRepository(Calendar::class)->calculatePendingForYear($user, date('Y')),
            'holidayTakenForYear'   => $em->getRepository(Calendar::class)->retrieveHolidayForYear($user, date('Y')),
            'overtime'       => $em->getRepository(Timesheet::class)->retrieveOvertimeForUser($user),
            'groupHolidays' => $groupHolidaysCount,
            'groupHolidaysList' => $groupHolidays,
            'groupHolidaysList2' => $groupHolidays2,
            'userHoliday' => $em->getRepository(Calendar::class)->retrieveListHolidayForYear($user, date('Y')),
            'table' => $table
        ]);
    }
    
}
