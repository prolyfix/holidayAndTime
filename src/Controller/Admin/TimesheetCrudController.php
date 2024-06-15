<?php

namespace App\Controller\Admin;

use App\Entity\Timesheet;
use App\Entity\User;
use App\Form\ConsumeTimeType;
use App\Form\TimesheetType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TimesheetCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $em, private AdminUrlGenerator $adminUrlGenerator)
    {}
    public static function getEntityFqcn(): string
    {
        return Timesheet::class;
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user'))
            ->add('startTime')
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user'),
            DateTimeField::new('startTime')->setFormat('dd.MM.YYYY HH:mm'),
            DateTimeField::new('endTime')->setFormat('dd.MM.YYYY HH:mm'),
            TimeField::new('break')->setFormat('HH:mm'),
            NumberField::new('overtime')->hideOnForm(),
        ];
    }
    #[Route('/admin/timesheet/add', name: 'admin_timesheet_add_time')]
    public function add(Request $request)
    {   
        $userId = $request->get('entityId');
        $user = $this->em->getRepository(User::class)->find($userId);
        $ts = new Timesheet();
        $ts->setUser($user);
        $form = $this->createForm(ConsumeTimeType::class, $ts);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            $time = $request->get('consume_time')['overTimeAsTime'];
            $ts->setOverTime(-($time['hour']*60 + $time['minute']));
            $em = $this->em;
            $em->persist($ts);
            $em->flush();
            $this->addFlash('success', 'Time consumed successfully');
            return $this->redirectToRoute('admin', [
                'crudAction' => 'show',
                'crudControllerFqcn' => 'App\Controller\Admin\UserCrudController',
                'entityId' => $user->getId()
            ]);
        }
        return $this->render('timesheet/consume_time.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
    ;
    }


    #[Route('/admin/timesheet/consume-time', name: 'admin_timesheet_consume_time')]
    public function consumeTime(Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {   
        $userId = $request->get('entityId');
        $user = $this->em->getRepository(User::class)->find($userId);
        $ts = new Timesheet();
        $ts->setUser($user);
        $form = $this->createForm(TimesheetType::class, $ts);
        $form->remove('user');
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($ts);
            $em->flush();
            return new RedirectResponse($urlGenerator->generate('admin',[
                'crudAction' => 'show',
                'crudControllerFqcn' => 'App\Controller\Admin\UserCrudController',
                'entityId' => $user->getId()
            ]));
            return $this->redirectToRoute('admin_timesheet_add_time');
        }
        return $this->render('timesheet/consume_time.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    public function edit(AdminContext $context)
    {   
        $request = $context->getRequest();
        $ts = $context->getEntity()->getInstance();
        $user = $this->getUser();
        $OK = false;
        if($user->hasRole(User::ROLE_ADMIN))
        {
            $OK = true;
        }elseif($user->hasRole(User::ROLE_MANAGER) and $ts->getUser()->getUsers()->contains($user))
        {
            $OK = true;
        }elseif($ts->getUser() == $user){
            $OK = true;
        }
        
        $form = $this->createForm(TimesheetType::class, $ts);
        $form->remove('user');
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $this->em->persist($ts);
            $this->em->flush();
            return new RedirectResponse($this->adminUrlGenerator
            ->setController(self::class)
            ->setAction('index')
            ->generateUrl()
            );
            return $this->redirectToRoute('admin_timesheet_add_time');
        }
        return $this->render('timesheet/consume_time.html.twig', [
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
            return $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)->andWhere('entity.user IN (:users)')
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
