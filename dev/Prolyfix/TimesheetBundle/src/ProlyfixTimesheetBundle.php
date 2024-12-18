<?php

namespace Prolyfix\TimesheetBundle;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\ModuleRight;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ProlyfixTimesheetBundle extends AbstractBundle implements ModuleInterface
{
    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'TimesheetBundle';
    }
    public static function getModuleName(): string
    {
        return 'Timesheet';
    }
    public static function getModuleDescription(): string
    {
        return 'Timesheet Module';
    }
    public static function getModuleType(): string
    {
        return 'module';
    }
    public static function getModuleConfiguration(): array
    {
        return [];
    }

    public static function getModuleRights(): array
    {
        return [];
    }

    public static function getMenuConfiguration(): array
    {
        return [];
    }

    public static function getUserConfiguration(): array
    {
        return [];
    }

    public static function getModuleAccess(): array
    {
        return [];
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../../../../config/services.yaml');

        // Load YAML or XML configuration files (optional)
        // $loader->load('services.yaml');

        // Register controllers manually
        $controllerNamespace = 'Prolyfix\\TimesheetBundle\\Controller\\';
        $resourceDir = dirname(__DIR__).'/src/Controller';

        $finder = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($resourceDir));

        foreach ($finder as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                $className = $controllerNamespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    substr($file->getPathname(), strlen($resourceDir) + 1)
                );

                // Define each controller as a service
                $container->services()
                    ->set($className, $className)
                    ->tag('controller.service_arguments')
                    ->tag('ea.crud_controller')
                    ->tag('controller.service_subscriber')
                    ->call('setContainer')
                    ->autowire(true)
                    ->autoconfigure(true)
                    ;

            }
        }
    }



}