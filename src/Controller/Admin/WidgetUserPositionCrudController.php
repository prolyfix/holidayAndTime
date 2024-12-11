<?php

namespace App\Controller\Admin;

use App\Entity\WidgetUserPosition;
use App\Event\ModifiableArrayEvent;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Utility\WidgetWalker;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\WidgetListEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment as Twig;
use Prolyfix\RssBundle\Widget\RssWidget;


class WidgetUserPositionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WidgetUserPosition::class;
    }


    public function configureWidgetPositions(WidgetWalker $widgetWalker, EventDispatcherInterface $eventDispatcher,EntityManagerInterface $em,  Security $security,  Twig $twig)
    {
        $availableWidgets = $widgetWalker->findWidgetClasses(__DIR__ . '/../../Widget');

        $newWidgetsFromBundles  = [];

        $event = new ModifiableArrayEvent($newWidgetsFromBundles);
        $eventDispatcher->dispatch($event, 'app.configure_widget_positions');
        $availableWidgetsFromBundle = $event->getData();

        $availableWidgets = array_merge($availableWidgets, $availableWidgetsFromBundle);
        foreach($availableWidgets as $widget) {
            if(!$widget->isForThisUserAvailable()) {
                unset($availableWidgets[array_search($widget, $availableWidgets)]);
            }
        }
        return $this->render('admin/widget_position/index.html.twig', [
            'availableWidgets' => $availableWidgets
        ]);
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
