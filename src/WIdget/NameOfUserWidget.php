<?php

namespace App\Widget;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class NameOfUserWidget implements WidgetInterface
{
    public function getName(): string
    {
        return 'Number of Users';
    }
    public function getWidth(): int
    {
        return 12;
    }
    public function getHeight(): int
    {
        return 3;
    }
    public function render(): string
    {
        $output ='<div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="card_bignumber">'. count($this->security->getUser()->getCompany()->getUsers()).'  </div>
                <h5 class="card-title">Number of User</h5>
                <a href="#" class="btn btn-primary">Add User</a>
            </div>
        </div>
    </div>';
        return $output;
    }
    public function getContext(): array
    {
        return [];
    }

    public function isForThisUserAvailable(): bool
    {
        return true;
    }

    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

}