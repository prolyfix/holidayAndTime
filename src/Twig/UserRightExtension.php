<?php
// src/AppBundle/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Configuration;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\WidgetUserPosition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Polyfill\Intl\Icu\DateFormat\MonthTransformer;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserRightExtension extends AbstractExtension
{
    public function __construct(private EntityManagerInterface $em , private Security $security, private Environment $twig) 
    {

    }
    public function getFunctions()
    {
        return [
            new TwigFunction('generateUserTabs', [$this, 'generateUserTabs']),
        ];
    }

    public function generateUserTabs($module)
    {
        $userConf = $module::getUserConfiguration();
        return $this->twig->render('admin/configuration/components/_user_tab.html.twig', ['userConf' => $userConf]);
    }
}