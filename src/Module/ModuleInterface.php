<?php
namespace  App\Modules;

interface ModuleInterface
{
    public static function getShortName(): string;
    public static function getModuleName(): string;
    public static function getModuleDescription(): string;
    public static function getModuleType(): string;
    public static function getModuleConfiguration(): array;
}