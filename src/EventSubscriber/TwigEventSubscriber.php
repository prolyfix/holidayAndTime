<?php

namespace App\EventSubscriber;

use App\Repository\HelpContentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{

    public function __construct(private Environment $twig, private HelpContentRepository $helpContentRepository)
    {

    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getUri();
        $this->twig->addGlobal('helpContent', $this->helpContentRepository->retrieveHelp($path));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
