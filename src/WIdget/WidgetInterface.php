<?php
namespace App\Widget;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;


interface WidgetInterface {
    public function getName(): string;
    public function getWidth(): int;
    public function getHeight(): int;
    public function render(): string;
    public function getContext(): array;
    public function isForThisUserAvailable(): bool;
    public function __construct(EntityManagerInterface $em, Security $security, Twig $twig);
}