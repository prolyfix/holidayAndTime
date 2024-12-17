<?php

namespace App\Prolyfix\NoteBundle;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\User;
use App\Module\ModuleInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Prolyfix\NoteBundle\Entity\Note;
use Prolyfix\RssBundle\Entity\RssFeedList;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ProlyfixNoteBundle extends AbstractBundle implements ModuleInterface
{
    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'NoteBundle';
    }
    public static function getModuleName(): string
    {
        return 'Note';
    }
    public static function getModuleDescription(): string
    {
        return 'Note Module';
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
                'class' => Company::class,
                'coverage' => 'user',
            ]
        ];
    }

    public static function getMenuConfiguration(): array
    {
        return [[
            'name' => 'Notes',
            'icon' => 'fa fa-rss',
            'route' => MenuItem::linkToCrud('note_list', 'fa fa-rss', Note::class),
            'order' => 1,
            'parent' => 'Notes',
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