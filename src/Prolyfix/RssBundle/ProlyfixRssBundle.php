<?php

namespace App\Prolyfix\RssBundle;

use App\Entity\Company;
use App\Entity\Module;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Prolyfix\RssBundle\Entity\RssFeedList;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ProlyfixRssBundle extends AbstractBundle implements ModuleInterface
{
    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'RssBundle';
    }
    public static function getModuleName(): string
    {
        return 'Rss';
    }
    public static function getModuleDescription(): string
    {
        return 'Rss Module';
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
            [
                'module' => Module::class,
                'module_action' => ['view', 'edit', 'delete', 'create', 'list'],
                'coverage' => 'company',
                'roles' => 'ROLE_USER'
            ]
        ];
    }

    public static function getMenuConfiguration(): array
    {
        return [[
            'name' => 'Rss',
            'icon' => 'fa fa-rss',
            'route' => MenuItem::linkToCrud('rss_feed_list', 'fa fa-rss', RssFeedList::class),
            'order' => 1,
            'parent' => 'Rss',
            'roles' => ['ROLE_USER'],
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