<?php

namespace App\Entity;

use App\Repository\TimesheetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimesheetRepository::class)]
class Timesheet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $break = null;

    #[ORM\ManyToOne(inversedBy: 'timesheets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $shouldHaveWorked = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $overtime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getBreak(): ?\DateTimeInterface
    {
        return $this->break;
    }

    public function setBreak(?\DateTimeInterface $break): static
    {
        $this->break = $break;

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

    public function getShouldHaveWorked(): ?\DateTimeInterface
    {
        return $this->shouldHaveWorked;
    }

    public function setShouldHaveWorked(?\DateTimeInterface $shouldHaveWorked): static
    {
        $this->shouldHaveWorked = $shouldHaveWorked;

        return $this;
    }

    public function getOvertime(): ?int
    {
        return $this->overtime;
    }

    public function setOvertime(?int $overtime): static
    {
        $this->overtime = $overtime;

        return $this;
    }
}
