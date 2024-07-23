<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar extends Commentable
{
    const STATE_PENDING = 'pending';
    const STATE_APPROVED = 'approved';
    const STATE_REFUSED = 'refused';
    
    public function __construct()
    {
        $this->state = self::STATE_PENDING;
    }

    #[ORM\Column(nullable: true)]
    private ?bool $isAll = null;

    #[ORM\ManyToOne(inversedBy: 'calendars')]
    private ?WorkingGroup $workingGroup = null;

    #[ORM\ManyToOne(inversedBy: 'calendars')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAfternoon = null;

    #[ORM\ManyToOne(inversedBy: 'calendars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeOfAbsence $typeOfAbsence = null;

    #[ORM\Column(nullable: true)]
    private ?bool $startMorning = null;

    #[ORM\Column(nullable: true)]
    private ?bool $endMorning = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?float $absenceInWorkingDays = null;


    public function isIsAll(): ?bool
    {
        return $this->isAll;
    }

    public function setIsAll(?bool $isAll): static
    {
        $this->isAll = $isAll;

        return $this;
    }

    public function getWorkingGroup(): ?WorkingGroup
    {
        return $this->workingGroup;
    }

    public function setWorkingGroup(?WorkingGroup $workingGroup): static
    {
        $this->workingGroup = $workingGroup;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isIsAfternoon(): ?bool
    {
        return $this->isAfternoon;
    }

    public function setIsAfternoon(?bool $isAfternoon): static
    {
        $this->isAfternoon = $isAfternoon;

        return $this;
    }

    public function getTypeOfAbsence(): ?TypeOfAbsence
    {
        return $this->typeOfAbsence;
    }

    public function setTypeOfAbsence(?TypeOfAbsence $typeOfAbsence): static
    {
        $this->typeOfAbsence = $typeOfAbsence;

        return $this;
    }

    public function getStartMorning(): ?bool
    {
        return $this->startMorning;
    }

    public function setStartMorning(bool $startMorning): static
    {
        $this->startMorning = $startMorning;

        return $this;
    }

    public function getEndMorning(): ?bool
    {
        return $this->endMorning;
    }

    public function setEndMorning(bool $endMorning): static
    {
        $this->endMorning = $endMorning;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getAbsenceInWorkingDays(): ?float
    {
        return $this->absenceInWorkingDays;
    }

    public function setAbsenceInWorkingDays(?float $absenceInWorkingDays): static
    {
        $this->absenceInWorkingDays = $absenceInWorkingDays;

        return $this;
    }
}
