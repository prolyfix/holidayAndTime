<?php 
namespace Prolyfix\RssBundle\Widget;

use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Prolyfix\NoteBundle\Entity\Note;
use Prolyfix\RssBundle\Entity\RssFeedEntry;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;

class NoteWidget implements WidgetInterface
{
    private EntityManagerInterface $em;
    private Security $security;
    private Twig $twig;

    public function getContext(): array
    {
        return [
            'title' => 'NoteWidget',
            'content' => 'NoteWidget content',
        ];
    }

    public function getTemplate(): string
    {
        return '@NoteBundle/widget/note_widget.html.twig';
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
        return 'NoteWidget';
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
        $notes = $this->em->getRepository(Note::class)->findBy([], ['date' => 'DESC'], 1);
        return 	$this->twig->render('@NoteBundle/widget/note_widget.html.twig',[
            'title' => 'NoteWidget',
            'content' => 'NoteWidget content',
            'notes' => $notes,
        ]);
    }
}
