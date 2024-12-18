<?php

namespace Prolyfix\RssBundle;

use App\Entity\ModuleRight;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Prolyfix\RssBundle\Entity\RssFeedEntry;
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