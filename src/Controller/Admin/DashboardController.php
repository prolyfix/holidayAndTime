<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\HelpContent;
use App\Entity\Issue;
use App\Entity\Media;
use App\Entity\Project;
use App\Entity\Room;
use App\Entity\Task;
use App\Entity\ThirdParty;
use App\Entity\Timesheet;
use App\Entity\TypeOfAbsence;
use App\Entity\User;
use App\Entity\Weekplan;
use App\Entity\WorkingGroup;
use Doctrine\ORM\EntityManagerInterface;
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
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        /*
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect(
            $adminUrlGenerator
                ->setController(UserCrudController::class)
                ->setAction('show')
                ->setEntityId($this->getUser()->getId())
                ->generateUrl()
        );
        */
        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard/index.html.twig',[
            'chart' => $chart,
            'todaysWorkers' => $todaysWorkers
        ]);
    }
    public function __construct(private EntityManagerInterface $em,private ChartBuilderInterface $chartBuilder)
    {
    }

    public function configureDashboard(): Dashboard
    {
        $title = "Holiday and Time";
        if($this->getUser()->getCompany() !== null){
            $title = "Holiday and Time - ". $this->getUser()->getCompany()->getName();
            if($this->getUser()->getCompany()->getLogoName() != null){
                $title = "<img src='uploads/logo/". $this->getUser()->getCompany()->getLogoName()."'>";
            }
        }
        return Dashboard::new()
            ->setTitle($title)
            ->setFaviconPath('images/favicon.ico')
            ->setFaviconPath('uploads/logo/'.$this->getUser()->getCompany()->getLogoName());

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('HR');
        yield MenuItem::linkToCrud('Calendar', 'fas fa-calendar', Calendar::class)->setAction('viewYear');
        yield MenuItem::linkToRoute('Holiday Requests', 'fas fa-route', 'admin_holiday_request', ['parameter' => 'value']);
        if($this->getUser()->isHasTimesheet()|| $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            yield MenuItem::linkToCrud('Timesheet', 'fas fa-hourglass', Timesheet::class);

        }        
        if($this->isGranted('ROLE_ADMIN')|| $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Benutzern');
            yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('Working Group', 'fas fa-users', WorkingGroup::class);
        }
        if($this->isGranted('ROLE_ADMIN')|| $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Configuration');
            yield MenuItem::linkToCrud('Type of Absence', 'fas fa-plane', TypeOfAbsence::class);
            yield MenuItem::linkToCrud('properties', 'fas fa-cog', Configuration::class)->setAction('showConfiguration');
        }
        yield MenuItem::section('Task');
        yield MenuItem::section('CRM');
        if( $this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('HelpContent', 'fas fa-user', HelpContent::class);
        }        
        $companyRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasCompany']);
        if($companyRight && $companyRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            yield MenuItem::linkToCrud('Customers', 'fas fa-house', Company::class);
        }
        $projectRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasProject','company'=>$this->getUser()->getCompany()]);
        if($projectRight && $projectRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            yield MenuItem::linkToCrud('Projects', 'fas fa-house', Project::class);
        }
        yield MenuItem::linkToCrud('Medias', 'fas fa-house', Media::class);
        $projectRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasTask','company'=>$this->getUser()->getCompany()]); 

        if($projectRight && $projectRight->getValue() == 1 || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            yield MenuItem::linkToCrud('Task', 'fas fa-house', Task::class);
        }
        yield MenuItem::linkToCrud('ThirdParty', 'fas fa-house', ThirdParty::class);
        $weekPlanningRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasWeekplan']);
        if($weekPlanningRight && $weekPlanningRight->getValue() == "true" || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            yield MenuItem::linkToCrud('Room', 'fas fa-house', Room::class);
            yield MenuItem::linkToCrud('Week Planning', 'fas fa-house', Weekplan::class);
        }
    }


    private function todaysWorkers(EntityManagerInterface $em, User $user): array
    {
        $output = [];
        $usersRaw = $em->getRepository(User::class)->findByCompany($user->getCompany());
        $today = new \DateTime();
        foreach($usersRaw as $user){
            $weekdayProperties = $user->getRightUserWeekdayProperties($today);
            foreach($weekdayProperties as $weekdayProperty){
                if($weekdayProperty->getWeekday() == $today->format('l')&& $weekdayProperty->getWorkingHours() !== null){
                    $output[] = $user;
                }
            }
        }        
        return $output;
    }

}
