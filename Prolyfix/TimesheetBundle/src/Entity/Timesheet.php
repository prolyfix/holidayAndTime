<?php

namespace Prolyfix\TimesheetBundle\Entity;

use App\Entity\TimeData;
use App\Entity\User;
use App\Entity\Commentable;
use App\Repository\TimesheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TimesheetRepository::class)]
class Timesheet  extends TimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
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

    #[ORM\Column(nullable: true)]
    private ?bool $alreadyCalculatedOnDay = null;

    #[ORM\Column(nullable: true)]
    private ?int $workingMinutes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDatetime = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBreak = null;


    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'commentable')]
    private Collection $timesheets;

    #[ORM\ManyToOne(inversedBy: 'relatedTimesheets')]
    private ?Commentable $relatedCommentable = null;


    public function __toString(): string
    {

        if ($this->relatedCommentable === null) {
            if($this->startTime == null) return $this->id;
            return $this->startTime->format('H:i') . ' - ' . $this->endTime->format('H:i');
        }
        $output =   $this->relatedCommentable . ' ' . $this->startTime->format('H:i');
        if($this->endTime == null) return $output;
        return $output. ' - ' . $this->endTime->format('H:i');
    }
    public function __construct()
    {
        $this->break = new \DateTime('00:00:00');
        $this->timesheets = new ArrayCollection();
    }

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

    public function isAlreadyCalculatedOnDay(): ?bool
    {
        return $this->alreadyCalculatedOnDay;
    }

    public function setAlreadyCalculatedOnDay(?bool $alreadyCalculatedOnDay): static
    {
        $this->alreadyCalculatedOnDay = $alreadyCalculatedOnDay;

        return $this;
    }

    public function getWorkingMinutes(): ?int
    {
        return $this->workingMinutes;
    }

    public function setWorkingMinutes(?int $workingMinutes): static
    {
        $this->workingMinutes = $workingMinutes;

        return $this;
    }

    public function getUpdateDatetime(): ?\DateTimeInterface
    {
        return $this->updateDatetime;
    }

    public function setUpdateDatetime(?\DateTimeInterface $updateDatetime): static
    {
        $this->updateDatetime = $updateDatetime;

        return $this;
    }

    public function isBreak(): ?bool
    {
        return $this->isBreak;
    }
    public function setIsBreak(?bool $isBreak): static
    {
        $this->isBreak = $isBreak;

        return $this;
    }


    /**
     * @return Collection<int, self>
     */
    public function getTimesheets(): Collection
    {
        return $this->timesheets;
    }

    public function addTimesheet(self $timesheet): static
    {
        if (!$this->timesheets->contains($timesheet)) {
            $this->timesheets->add($timesheet);
        }

        return $this;
    }

    public function removeTimesheet(self $timesheet): static
    {
        if ($this->timesheets->removeElement($timesheet)) {
            // set the owning side to null (unless already changed)
        }

        return $this;
    }

    public function getRelatedCommentable(): ?Commentable
    {
        return $this->relatedCommentable;
    }

    public function setRelatedCommentable(?Commentable $relatedCommentable): static
    {
        $this->relatedCommentable = $relatedCommentable;

        return $this;
    }



}
