<?php
namespace App\Controller\Admin;

use App\Entity\ModuleAccess;
use App\Entity\ModuleConfigurationValue;
use App\Entity\ModuleRight;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

abstract class BaseCrudController extends AbstractCrudController
{
    protected Security $security;

    public function __construct(Security $security, private EntityManagerInterface $em)
    {
        $this->security = $security;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $query = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->security->getUser();
        $company = $user->getCompany();
        $moduleRights = $this->em->getRepository(ModuleRight::class)->findBy([
            'entityClass' => $entityDto->getFqcn(),
            'appliedToCompany' => $company
        ]);
        
        if(!$this->em->getRepository(ModuleConfigurationValue::class)->hasModuleEnabled($company, $moduleRights[0]->getModule())) {
            throw new AccessDeniedException('Module is not enabled');
        }

        if(!$this->em->getRepository(ModuleAccess::class)->hasUserAccessToModule($user, $moduleRights[0]->getModule())){
            throw new AccessDeniedException('User does not have access to this module');
        }

        $limitations = $this->em->getRepository(ModuleRight::class)->getModuleRightsForUserAndTable($moduleRights[0]->getModule(), $entityDto->getFqcn(), $user);
        if(count($limitations) > 0) {
            foreach($limitations as $limitation)
            {
                switch($limitation->getCoverage()){
                    case 'ROLE_ADMIN':
                        break;
                    case 'user':
                        $query->andWhere('entity.createdBy = :current_user')
                            ->setParameter('current_user', $user);
                        break;
                    case 'company':
                        $query->join('entiy.createdBy', 'user')  
                        ->andWhere('user.company = :current_company')
                            ->setParameter('current_company', $company);
                        break;
                    case 'working_group':
                        $query->join('entiy.createdBy', 'user') 
                            ->andWhere('user.workingGroup = :current_working_group')
                            ->setParameter('current_working_group', $user->getWorkingGroup());
                        break;
                    }
            }
        }
        return $query;
    }
}
