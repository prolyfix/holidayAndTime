<?php

namespace Prolyfix\TimesheetBundle;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\ModuleRight;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Prolyfix\RssBundle\Entity\RssFeedEntry;
use Prolyfix\RssBundle\Entity\RssFeedList;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
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
        return [
            (new ModuleRight())
                ->setModuleAction(['list', 'show', 'edit', 'new', 'delete'])
                ->setCoverage('user')
                ->setRole('ROLE_USER')
                ->setEntityClass(RssFeedList::class),
            (new ModuleRight())
                ->setModuleAction(['list', 'show', 'edit', 'new', 'delete'])
                ->setCoverage('company')
                ->setRole('ROLE_ADMIN')
                ->setEntityClass(RssFeedEntry::class),
        ];
    }

    public static function getMenuConfiguration(): array
    {
        return ['miscalleanouss' => [
            MenuItem::linkToCrud('Rss Feed List', 'fas fa-list', RssFeedList::class),
        ]];
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