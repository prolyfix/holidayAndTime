<?php
namespace App\Module;

use App\Module\ModuleInterface;

class ProjectModule implements ModuleInterface
{
    public static function getShortName(): string
    {
        return 'project';
    }

    public static function getModuleName(): string
    {
        return 'Project Module';
    }
    public static function getModuleDescription(): string
    {
        return 'Project Module';
    }
    public static function getModuleType(): string
    {
        return 'module';
    }
    public static function getModuleConfiguration(): array
    {
        return [];
    }
    public static function getModuleRights():array
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
}

