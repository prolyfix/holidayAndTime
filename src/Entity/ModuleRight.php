<?php

namespace App\Entity;

use App\Repository\ModuleRightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRightRepository::class)]
class ModuleRight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $moduleAction = [];

    #[ORM\Column(length: 255)]
    private ?string $coverage = null;


    #[ORM\ManyToOne(inversedBy: 'moduleRIghts')]
    private ?Company $appliedToCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entityClass = null;

    #[ORM\ManyToOne(inversedBy: 'moduleRights')]
    private ?Module $module = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleAction(): array
    {
        return $this->moduleAction;
    }

    public function setModuleAction(array $moduleAction): static
    {
        $this->moduleAction = $moduleAction;

        return $this;
    }

    public function getCoverage(): ?string
    {
        return $this->coverage;
    }

    public function setCoverage(string $coverage): static
    {
        $this->coverage = $coverage;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function setEntityClass(?string $entityClass): static
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

}
