<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\ModuleAccess;
use App\Entity\ModuleConfiguration;
use App\Entity\ModuleConfigurationValue;
use App\Entity\ModuleRight;
use App\Entity\User;
use App\Entity\WorkingGroup;
use App\Manager\ConfigurationUpdater;
use App\Utility\ModuleWalker;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Request;

class ModuleConfigurationValueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModuleConfigurationValue::class;
    }

    public function showConfiguration(ConfigurationUpdater $configurationUpdater, ModuleWalker $moduleWalker, EntityManagerInterface $em)
    {   

        //registration of new Modules: to do: move this to a command
        $modules = $moduleWalker->findModuleClasses(__DIR__.'/../../Module');
        $notRegisteredModules = $em->getRepository(Module::class)->ListModulesNotRegistered($modules);
        foreach($notRegisteredModules as $key=>$moduleClass)
        {
            $module = new Module();
            $module->setName($key);
            $module->setClass($moduleClass);
            $em->persist($module);
        }
        $em->flush();
        // retrieve All Modules
        $modules = $em->getRepository(Module::class)->findAll();
        //foreach Modules, verify if there is a ModuleConfiguration with name active and type boolean, if not create it
        foreach ($modules as $module) {
            $configuration = $em->getRepository(ModuleConfiguration::class)->findOneBy([
                'module' => $module,
                'name' => 'active',
                'type' => 'boolean'
            ]);

            if (!$configuration) {
                $configuration = new ModuleConfiguration();
                $configuration->setModule($module);
                $configuration->setName('active');
                $configuration->setType('boolean');
                
                $em->persist($configuration);
            }
        }

        $activeConfigurations = $em->getRepository(ModuleConfiguration::class)->findBy([
            'name' => 'active',
            'type' => 'boolean'
        ]);
        foreach($activeConfigurations as $activeConfiguration)
        {
            $valueExists = $em->getRepository(ModuleConfigurationValue::class)->findOneBy([
                'moduleConfiguration' => $activeConfiguration,
                'relatedClass'        => Company::class,
                'relatedId'           => $this->getUser()->getCompany()->getId()
            ]);
            if(!$valueExists)
            {
                $value = new ModuleConfigurationValue();
                $value->setModuleConfiguration($activeConfiguration);
                $value->setRelatedClass(Company::class);
                $value->setRelatedId($this->getUser()->getCompany()->getId());
                $value->setValue(["1"]);
                $em->persist($value);
            }
        }

        // fin de l'initialisation des configurations
        // récupération des configurations propres à l'entreprise
        // depending on the role, we can have different configurations
        $configurations = $em->getRepository(ModuleConfigurationValue::class)->findBy([
            'relatedClass' => Company::class,
            'relatedId' => $this->getUser()->getCompany()->getId()
        ]);
        $em->flush();
        return $this->render('admin/configuration/show.html.twig',[
            'page_title'=>'Configuration',
            'content_title'=>'Configuration',
            'configurations'=>$configurations
        ]);
    }

    public function showModuleConfiguration( Request $request, EntityManagerInterface $em)
    {
        $moduleConfigurationValueId = $request->get('entityId');
        $entity = $em->getRepository(ModuleConfigurationValue::class)->find($moduleConfigurationValueId);
        $moduleEntity = $entity->getModuleConfiguration()->getModule();
        $module = new ($moduleEntity->getClass());
        $moduleRights = $module::getModuleRights();
        foreach($moduleRights as $moduleRight)
        {
            $moduleRight->setAppliedToCompany($this->getUser()->getCompany());
            $moduleRight->setModule($moduleEntity);
            $em->persist($moduleRight);
        }
        $em->flush();

        
        $moduleAccesses = $module::getModuleAccess();
        foreach($moduleAccesses as $moduleAccess)
        {
            $em->persist($moduleAccess);
        }
        //temp: to be removed
        $moduleAccess = (new ModuleAccess())
            ->setModule($moduleEntity)
            ->setTenantClass(Company::class)
            ->setTenantId($this->getUser()->getCompany()->getId())

            ;
        $em->persist($moduleAccess);
        $em->flush();

        if ($this->isGranted('ROLE_ADMIN')) {
            $relatedClass = Company::class;
            $relatedId = $this->getUser()->getCompany()->getId();
        }
        elseif($this->isGranted('ROLE_MANAGER')){
            $relatedClass = WorkingGroup::class;
            $relatedId = $this->getUser()->getWorkingGroup()->getId();
        }
        elseif($this->isGranted('ROLE_USER')){
            $relatedClass = User::class;
            $relatedId = $this->getUser()->getId();
        }

        $moduleConfigurationValue = $em->getRepository(ModuleConfigurationValue::class)->findFromModule([
            'module'        => $moduleEntity,
            'relatedClass'  => $relatedClass,
            'relatedId'     => $relatedId,
        ]);

        $moduleAccess = $em->getRepository(ModuleAccess::class)->findBy([
            'module' => $moduleEntity,
            'tenantClass' => $relatedClass,
            'tenantId' => $relatedId
        ]);

        return $this->render('admin/configuration/showModuleConfiguration.html.twig',[
            'page_title'=>'Configuration',
            'content_title'=>'Configuration',
            'moduleConfigurationValues'=>$moduleConfigurationValue,
            'moduleRights'=>$moduleRights,
            'module'=>$module,
            'moduleAccess'=>$moduleAccess

        ]);



    }
}
