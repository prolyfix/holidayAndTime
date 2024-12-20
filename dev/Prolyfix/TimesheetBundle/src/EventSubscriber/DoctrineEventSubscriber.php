<?php
namespace Prolyfix\TimesheetBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        if ($classMetadata->getName() === 'App\Entity\User') {
            $this->addTimesheetMapping($classMetadata);
        }
    }

    private function addTimesheetMapping(ClassMetadata $classMetadata): void
    {
        if (!$classMetadata->hasAssociation('timesheets')) {
            $classMetadata->mapOneToMany([
                'targetEntity' => 'App\Entity\Timesheet',
                'mappedBy' => 'user',
                'cascade' => ['persist', 'remove'],
                'orphanRemoval' => true,
            ]);
        }
    }
}