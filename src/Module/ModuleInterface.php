<?php
namespace  App\Module;

interface ModuleInterface
{
    public static function getShortName(): string;
    public static function getModuleName(): string;
    public static function getModuleDescription(): string;
    public static function getModuleType(): string;
    public static function getModuleConfiguration(): array;
    public static function getModuleRights():array;

    public static function getMenuConfiguration(): array;

    public static function getUserConfiguration(): array;
}