<?php

namespace App\Prolyfix\NoteBundle;

use App\Entity\Company;
use App\Entity\Module;
use App\Entity\ModuleRight;
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
            (new ModuleRight())
                ->setModuleAction(['list', 'show', 'edit', 'new', 'delete'])
                ->setCoverage('user')
                ->setRole('ROLE_USER')
                ->setEntityClass(Note::class),
        ];
    }

    public static function getMenuConfiguration(): array
    {
        return ['miscalleanous' => [
            MenuItem::linkToCrud('Note List', 'fas fa-list', Note::class),
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