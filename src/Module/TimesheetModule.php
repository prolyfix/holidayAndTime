<?php
namespace  App\Module;

use App\Modules\ModuleInterface;

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
}