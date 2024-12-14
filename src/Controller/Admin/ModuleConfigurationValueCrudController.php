<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Module;
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

        //verification de l'existence des droits pour ce module. Si inexistant: création
        $moduleConfigurationValue = $em->getRepository(ModuleConfigurationValue::class)->findFromModule([
            'moduleId' => $moduleEntity->getId(),
            'relatedClass' => Company::class,
            'relatedId' => $this->getUser()->getCompany()->getId()
        ]);

        $moduleRightsSaved = $em->getRepository(ModuleRight::class)->findFromModule($module::class, $this->getUser()->getCompany()->getId());
        if(count($moduleRightsSaved) == 0 && count($moduleRights) > 0)
        {
            foreach($moduleRights as $moduleRight)
            {
                $relatedId = null;
                switch($moduleRight['class']){
                    case Company::class:
                        $relatedId = $this->getUser()->getCompany()->getId();
                        break;
                    case User::class:
                        $relatedId = $this->getUser()->getId();
                        break;
                    case WorkingGroup::class:
                        $relatedId = $this->getUser()->getWorkingGroup()->getId();
                        break;
                }


                $right = new ModuleRight();
                $right->setModule($moduleEntity);
                $right->setModuleAction($moduleRight['module_action']);
                $right->setClass($moduleRight['class']);
                $right->setCoverage($moduleRight['coverage']);
                $right->setRelatedId($relatedId);
                $right->setAppliedToCompany($this->getUser()->getCompany());
                $em->persist($right);
            }
            $em->flush();
        }

        
        $moduleRights = $em->getRepository(ModuleRight::class)->findFromModule($module::class, $this->getUser()->getCompany()->getId());
        return $this->render('admin/configuration/showModuleConfiguration.html.twig',[
            'page_title'=>'Configuration',
            'content_title'=>'Configuration',
            'moduleConfigurationValues'=>$moduleConfigurationValue,
            'moduleRights'=>$moduleRights,
            'module'=>$module
        ]);



    }
}
