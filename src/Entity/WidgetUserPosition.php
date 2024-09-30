<?php

namespace App\Entity;

use App\Repository\WidgetUserPositionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetUserPositionRepository::class)]
class WidgetUserPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $widgetClass = null;

    #[ORM\ManyToOne(inversedBy: 'widgetUserPositions')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $crudControllerFqcn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $crudAction = null;

    #[ORM\Column(nullable: true)]
    private ?int $rowIndex = null;

    #[ORM\Column(nullable: true)]
    private ?int $columIndex = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWidgetClass(): ?string
    {
        return $this->widgetClass;
    }

    public function setWidgetClass(string $widgetClass): static
    {
        $this->widgetClass = $widgetClass;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCrudControllerFqcn(): ?string
    {
        return $this->crudControllerFqcn;
    }

    public function setCrudControllerFqcn(?string $crudControllerFqcn): static
    {
        $this->crudControllerFqcn = $crudControllerFqcn;

        return $this;
    }

    public function getCrudAction(): ?string
    {
        return $this->crudAction;
    }

    public function setCrudAction(?string $crudAction): static
    {
        $this->crudAction = $crudAction;

        return $this;
    }

    public function getRowIndex(): ?int
    {
        return $this->rowIndex;
    }

    public function setRowIndex(?int $rowIndex): static
    {
        $this->rowIndex = $rowIndex;

        return $this;
    }

    public function getColumIndex(): ?int
    {
        return $this->columIndex;
    }

    public function setColumIndex(?int $columIndex): static
    {
        $this->columIndex = $columIndex;

        return $this;
    }
}
