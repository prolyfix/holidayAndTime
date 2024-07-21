<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Entity\Weekplan;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class WeekplanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Weekplan::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('weekday')->setChoices([
                'Monday' => 'Monday',
                'Tuesday' => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday',
                'Friday' => 'Friday',
                'Saturday' => 'Saturday',
                'Sunday' => 'Sunday',
            ]),
            TimeField::new('startTime'),
            TimeField::new('endTime'),
            AssociationField::new('user'),
            AssociationField::new('room'),
        ];
    }
    
    function showPlanning( EntityManagerInterface $em)
    {
        $rooms = $em->getRepository(Room::class)->findAll();

        return $this->render('calendar/planning.html.twig', ['rooms' => $rooms]);
    }

}
