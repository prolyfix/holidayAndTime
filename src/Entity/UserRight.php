<?php

namespace App\Entity;

use App\Repository\UserRightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRightRepository::class)]
class UserRight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?array $rights = null;

    #[ORM\ManyToOne(inversedBy: 'userRights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $appliedToCompany = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRights(): ?array
    {
        return $this->rights;
    }

    public function setRights(?array $rights): static
    {
        $this->rights = $rights;

        return $this;
    }

    public function getAppliedToCompany(): ?Company
    {
        return $this->appliedToCompany;
    }

    public function setAppliedToCompany(?Company $appliedToCompany): static
    {
        $this->appliedToCompany = $appliedToCompany;

        return $this;
    }
}
