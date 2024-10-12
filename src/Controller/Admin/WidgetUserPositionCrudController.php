<?php

namespace App\Controller\Admin;

use App\Entity\WidgetUserPosition;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Utility\WidgetWalker;

class WidgetUserPositionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WidgetUserPosition::class;
    }

    public function configureWidgetPositions(WidgetWalker $widgetWalker)
    {
        $availableWidgets = $widgetWalker->findWidgetClasses(__DIR__ . '/../../Widget');
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
