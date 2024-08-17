<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Entity\UserWeekdayProperty;
use App\Form\UserPropertyType;
use App\Form\UserScheduleType;
use App\Form\UserType;
use App\Form\UserWeekdayPropertyType;
use App\Manager\HolidayCalculator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use SebastianBergmann\Template\Template;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

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
            BooleanField::new('emailInteraction')

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
            ->andWhere('id = :userId')
            ->setParameter('userID', $user->getId());

        return $queryBuilder;
    }

    public function show( HolidayCalculator $holidayCalculator, EntityManagerInterface $em): Response
    {

        $user = $this->getContext()->getEntity()->getInstance();
        $groupHolidays = $em->getRepository(Calendar::class)->retrieveHolidaysForGroupForYear($user->getWorkingGroup(), date('Y'));
        $groupHolidaysCount = 0;
        foreach($groupHolidays as $holiday){
            $holiday->setUser($user);
            $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
            $groupHolidaysCount += $days;
            $holiday->setAbsenceInWorkingDays($days);
        }
        $groupHolidays2 = $em->getRepository(Calendar::class)->retrieveHolidaysForFirmForYear( date('Y'));
        foreach($groupHolidays2 as $holiday){
            $holiday->setUser($user);
            $days = $holidayCalculator->calculateEffectiveWorkingDays2($holiday, true);
            $groupHolidaysCount += $days;
            $holiday->setAbsenceInWorkingDays($days);
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

        ]);
    }
    
}
