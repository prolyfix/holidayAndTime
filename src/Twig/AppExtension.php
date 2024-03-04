<?php
// src/AppBundle/Twig/AppExtension.php
namespace App\Twig;

use Symfony\Polyfill\Intl\Icu\DateFormat\MonthTransformer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isWeekend', [$this, 'isWeekend']),
            new TwigFunction('minutesToTime', [$this, 'minutesToTime']),
            new TwigFunction('weekday', [$this, 'weekday']),
            new TwigFunction('calculateDiff', [$this, 'calculateDiff']),
        ];
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