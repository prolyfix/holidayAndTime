<?php
namespace Prolyfix\NoteBundle\EventListener;

use App\Event\ModifiableArrayEvent;
use Doctrine\ORM\EntityManagerInterface;
use Prolyfix\RssBundle\Widget\NoteWidget;
use Prolyfix\RssBundle\Widget\RssWidget;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;

class AddWidgetNoteListener
{
    public function __construct(private EntityManagerInterface $em, private Security $security, private Twig $twig)
    {
    }

    public function onAppConfigureWidgetPositions(ModifiableArrayEvent $event)
    {
        $availableWidgets = $event->getData();
        $availableWidgets[] = new NoteWidget($this->em, $this->security, $this->twig);
        $event->setData($availableWidgets);
    }
}