<?php

namespace App\Controller\Admin;

use App\Entity\Appointment;
use App\Entity\Calendar;
use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\Contact;
use App\Entity\DummyEntity;
use App\Entity\HelpContent;
use App\Entity\Issue;
use App\Entity\Media;
use App\Entity\Project;
use App\Entity\Room;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\ThirdParty;
use App\Entity\Timesheet;
use App\Entity\TypeOfAbsence;
use App\Entity\User;
use App\Entity\Weekplan;
use App\Entity\WidgetUserPosition;
use App\Entity\WorkingGroup;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        $todaysWorkers = $this->todaysWorkers($this->em, $this->getUser());

        $workingTimeThisWeek = $this->getUser()->getRightUserWeekdayProperties(new \DateTime());
        $timesheetThisWeek = $this->em->getRepository(Timesheet::class)->getWeekTimesheet($this->getUser());

        return $this->render('admin/dashboard/index.html.twig', [
            'chart' => $chart,
            'todaysWorkers' => $todaysWorkers,
            'workloadToday' => $this->getWorkedHoursToday(),
            'workingTimeThisWeek' => $workingTimeThisWeek,
            'timesheetThisWeek' => $timesheetThisWeek
        ]);
    }
    public function __construct(private EntityManagerInterface $em, private ChartBuilderInterface $chartBuilder) {}

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('style/admin.css');
    }

    public function configureDashboard(): Dashboard
    {
        $title = "Holiday and Time";
        if ($this->getUser()->getCompany() !== null) {
            $title = "Holiday and Time - " . $this->getUser()->getCompany()->getName();
            if ($this->getUser()->getCompany()->getLogoName() != null) {
                $title = "<img src='uploads/logo/" . $this->getUser()->getCompany()->getLogoName() . "'>";
            }
            return Dashboard::new()
                ->setTitle($title)
                ->setFaviconPath('images/favicon.ico')
                ->setFaviconPath('uploads/logo/' . $this->getUser()->getCompany()->getLogoName());
        }
        return parent::configureDashboard();
    }

    public function getWorkedHoursToday(): iterable
    {
        $workedHours = $this->em->getRepository(Timesheet::class)->getWorkedHoursToday($this->getUser());
        return $workedHours;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if ($this->getUser()->getCompany() !== null) {
            yield MenuItem::section('HR');
            yield MenuItem::linkToCrud('Calendar', 'fas fa-calendar', Calendar::class)->setAction('viewYear');
            yield MenuItem::linkToRoute('Holiday Requests', 'fas fa-route', 'admin_holiday_request', ['parameter' => 'value']);
            if ($this->getUser()->isHasTimesheet() || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                yield MenuItem::linkToCrud('Timesheet', 'fas fa-hourglass', Timesheet::class);
            }
            if ($this->isGranted('ROLE_ADMIN') || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                yield MenuItem::section('Benutzern');
                yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
                yield MenuItem::linkToCrud('Working Group', 'fas fa-users', WorkingGroup::class);
            }
        }
        $projectRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasProject', 'company' => $this->getUser()->getCompany()]);
        $taskRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasTask', 'company' => $this->getUser()->getCompany()]);
        if ($projectRight && $projectRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN') || $taskRight && $taskRight->getValue() == 1) {
            yield MenuItem::section('Projects');
        }
        $belongsToProject = false;
        if ($this->getUser()->getCommentableMembers()->count() > 0) {
            $belongsToProject = true;
        }
        if ($projectRight && $projectRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN' || $belongsToProject)) {
            yield MenuItem::linkToCrud('Projects', 'fas fa-diagram-project', Project::class);
        }

        if ($taskRight && $taskRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Task', 'fas fa-check', Task::class)->setQueryParameter('filters[status]', "new");
        }

        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Customers', 'fas fa-house', Company::class);
        }
        $companyRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasCRM', 'company' => $this->getUser()->getCompany()]);

        if ($companyRight && $companyRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('CRM');
            yield MenuItem::linkToCrud('Medias', 'fas fa-photo-film', Media::class);
            yield MenuItem::linkToCrud('Contact', 'fas fa-address-book', Contact::class);
            yield MenuItem::linkToCrud('ThirdParty', 'fas fa-handshake', ThirdParty::class);
            yield MenuItem::linkToCrud('Appointments', 'fas fa-calendar', Appointment::class);
        }
        $weekPlanningRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasWeekplan']);
        if ($this->getUser()->getCompany()!==null && ($weekPlanningRight && $weekPlanningRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN'))) {
            yield MenuItem::section('Week planning');
            yield MenuItem::linkToCrud('Room', 'fas fa-people-roof', Room::class);
            yield MenuItem::linkToCrud('Week Planning', 'fas fa-ruler', Weekplan::class);
        }
        if ($this->isGranted('ROLE_ADMIN') || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Configuration');
            yield MenuItem::linkToCrud('Type of Absence', 'fas fa-plane', TypeOfAbsence::class);
            yield MenuItem::linkToCrud('properties', 'fas fa-cog', Configuration::class)->setAction('showConfiguration');
            yield MenuItem::linkToCrud('tags', 'fas fa-cog', Tag::class)->setAction('index');
        }
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('HelpContent', 'fas fa-user', HelpContent::class);
        }
        yield MenuItem::linkToCrud('widgets', 'fas fa-cog', WidgetUserPosition::class)->setAction(actionName: 'configureWidgetPositions');
    }


    private function todaysWorkers(EntityManagerInterface $em, User $user): array
    {
        $output = [];
        $usersRaw = $em->getRepository(User::class)->findByCompany($user->getCompany());
        $today = new \DateTime();
        foreach ($usersRaw as $user) {
            $weekdayProperties = $user->getRightUserWeekdayProperties($today);
            foreach ($weekdayProperties as $weekdayProperty) {
                if ($weekdayProperty->getWeekday() == $today->format('l') && $weekdayProperty->getWorkingHours() !== null) {
                    $output[] = $user;
                }
            }
        }
        return $output;
    }
}
