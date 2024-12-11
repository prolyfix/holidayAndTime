<?php
namespace App\Manager;

use App\Event\ModifiableArrayEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConfigurationUpdater
{
    public function __construct(private  EventDispatcherInterface $eventDispatcher, private EntityManagerInterface $em)
    {
    }
    public function getConfigurationList(): array
    {
        $list = [];
        $event = new ModifiableArrayEvent($list);
        $this->eventDispatcher->dispatch($event, 'app.get_configuration');
        $list = $event->getData();
        return $list;
    }

    public function updateConfigurationList(array $list): void
    {
        foreach($list as $key => $value){
            $this->em->persist($value);
        }
    }

}