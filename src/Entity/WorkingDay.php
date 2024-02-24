<?php

namespace App\Entity;

use App\Repository\WorkingDayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkingDayRepository::class)]
class WorkingDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $workingDay = null;

    #[ORM\Column(nullable: true)]
    private ?float $workingHours = null;

    #[ORM\ManyToOne(inversedBy: 'workingDays')]
    private ?User $relativeUser = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $weekday = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkingDay(): ?float
    {
        return $this->workingDay;
    }

    public function setWorkingDay(?float $workingDay): static
    {
        $this->workingDay = $workingDay;

        return $this;
    }

    public function getWorkingHours(): ?float
    {
        return $this->workingHours;
    }

    public function setWorkingHours(?float $workingHours): static
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    public function getRelativeUser(): ?User
    {
        return $this->relativeUser;
    }

    public function setRelativeUser(?User $relativeUser): static
    {
        $this->relativeUser = $relativeUser;

        return $this;
    }

    public function getWeekday(): ?string
    {
        return $this->weekday;
    }

    public function setWeekday(?string $weekday): static
    {
        $this->weekday = $weekday;

        return $this;
    }
}
