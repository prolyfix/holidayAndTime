<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\Issue;
use App\Entity\Project;
use App\Entity\Room;
use App\Entity\Task;
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

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect(
            $adminUrlGenerator
                ->setController(UserCrudController::class)
                ->setAction('show')
                ->setEntityId($this->getUser()->getId())
                ->generateUrl()
        );

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    public function __construct(private EntityManagerInterface $em){
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('HolidayAndTime');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Calendar', 'fas fa-calendar', Calendar::class)->setAction('viewYear');
        yield MenuItem::linkToRoute('Holiday Requests', 'fas fa-route', 'admin_holiday_request', ['parameter' => 'value']);
        if($this->getUser()->isHasTimesheet()){
            yield MenuItem::linkToCrud('Timesheet', 'fas fa-hourglass', Timesheet::class);

        }
        if($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('Type of Absence', 'fas fa-plane', TypeOfAbsence::class);
            yield MenuItem::linkToCrud('Working Group', 'fas fa-users', WorkingGroup::class);
            yield MenuItem::linkToCrud('properties', 'fas fa-cog', Configuration::class)->setAction('showConfiguration');
        }
        $companyRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasCompany']);
        if($companyRight && $companyRight->getValue() == 1){
            yield MenuItem::linkToCrud('Customers', 'fas fa-house', Company::class);
        }
        $projectRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasProject']);
        if($projectRight && $projectRight->getValue() == 1){
            yield MenuItem::linkToCrud('Projects', 'fas fa-house', Project::class);
        }
        $projectRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasTask','company'=>$this->getUser()->getCompany()]); 
        if($projectRight && $projectRight->getValue() == 1){
            yield MenuItem::linkToCrud('Task', 'fas fa-house', Task::class);
        }
        $weekPlanningRight = $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasWeekplan']);
        if($weekPlanningRight && $weekPlanningRight->getValue() == "true"){
            yield MenuItem::linkToCrud('Room', 'fas fa-house', Room::class);
            yield MenuItem::linkToCrud('Week Planning', 'fas fa-house', Weekplan::class);
        }
    }
}
