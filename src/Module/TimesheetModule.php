<?php
namespace  App\Module;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\Timesheet;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

class TimesheetModule implements ModuleInterface
{
    public static function getShortName(): string
    {
        return 'TimesheetModule';
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

    public static function getMenuConfiguration(): array
    {
        return ['timesheet'=>[
                MenuItem::section('Timesheet', 'fas fa-clock'),
                MenuItem::linkToCrud('List', 'fas fa-list', Timesheet::class),
            ]
        ];
    }

    public static function getModuleRights(): array
    {
        return [
            [
                'module' => Module::class,
                'module_action' => ['view', 'edit', 'delete', 'create', 'list'],
                'class' => Company::class,
                'coverage' => 'company',
                
            ]
        ];
    }

    public static function getUserConfiguration(): array
    {
        return [];
    }

    public static function getModuleAccess(): array
    {
        return [];
    }
}