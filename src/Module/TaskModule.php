<?php
namespace App\Module;

use App\Module\ModuleInterface;

class TaskModule implements ModuleInterface
{
    public static function getShortName(): string
    {
        return 'task';
    }

    public static function getModuleName(): string
    {
        return 'Task Module';
    }
    public static function getModuleDescription(): string
    {
        return 'Task Module';
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

