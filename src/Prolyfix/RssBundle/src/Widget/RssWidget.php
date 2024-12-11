<?php 
namespace Prolyfix\RssBundle\Widget;

use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;

class RssWidget implements WidgetInterface
{
    private EntityManagerInterface $em;
    private Security $security;
    private Twig $twig;

    public function getContext(): array
    {
        return [
            'title' => 'RssWidget',
            'content' => 'RssWidget content',
        ];
    }

    public function getTemplate(): string
    {
        return '@RssBundle/widget/rss_widget.html.twig';
    }

    public function getHeight(): int
    {
        return 200;
    }

    public function getWidth(): int
    {
        return 200;
    }   

    public function getName(): string
    {
        return 'RssWidget';
    }

    public function isForThisUserAvailable(): bool
    {
        return true;
    }
    public function __construct(EntityManagerInterface $em, Security $security, Twig $twig)
    {
        $this->em = $em;
        $this->security = $security;
        $this->twig = $twig;
    }

    public function render(): string
    {
        return 'RssWidget';
    }
}
