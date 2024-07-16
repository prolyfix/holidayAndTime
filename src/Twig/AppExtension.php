<?php
// src/AppBundle/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Configuration;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Polyfill\Intl\Icu\DateFormat\MonthTransformer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private EntityManagerInterface $em)
    {

    }
    public function getFunctions()
    {
        return [
            new TwigFunction('isWeekend', [$this, 'isWeekend']),
            new TwigFunction('minutesToTime', [$this, 'minutesToTime']),
            new TwigFunction('weekday', [$this, 'weekday']),
            new TwigFunction('toTime', [$this, 'toTime']),
            new TwigFunction('calculateDiff', [$this, 'calculateDiff']),
            new TwigFunction('isWorkday', [$this, 'isWorkday']),
            new TwigFunction('hasCommentInTime', [$this, 'hasCommentInTime'])
        ];
    }

    public function hasCommentInTime():bool
    {
        $conf = $this->em->getRepository(Configuration::class)->findOneByName('commentInTime');
        if($conf == null)
            return false;
        return($conf->getValue() == 1);
        
    }
    public function isWorkday(User $user, string $date)
    {
        $trans = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
        $realDate = \DateTime::createFromFormat('Y-m-d', $date);
        if(!$realDate)
            return false;
        $workdays = $user->getUserWeekdayProperties();
        foreach($workdays as $workday){
            if($workday->getWeekday() == $trans[$realDate->format('N')] && $workday->getWorkingDay() > 0){
                return true;
            }
        }
        return false;
    }
    public function toTime(int $minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
    public function weekday($day, $month, $year)
    {
        $date = new \DateTime();
        $date->setDate($year, $month, $day);
        return substr($date->format('l'),0,1);
    }

    public function calculateDiff($monthBegin)
    {
        $weekday = $monthBegin->format('N');
      
        return $weekday;
    }


    public function minutesToTime(int $minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
    
    public function isWeekend(string $date)
    {
        $realDate = \DateTime::createFromFormat('Y-m-d', $date);
        if(!$realDate)
            return false;
        return $realDate->format('N') >= 6;
    }
}