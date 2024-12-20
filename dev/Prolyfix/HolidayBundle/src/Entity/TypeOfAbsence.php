<?php

namespace App\Entity;

use App\Repository\TypeOfAbsenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeOfAbsenceRepository::class)]
class TypeOfAbsence extends TimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isHoliday = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isTimeHoliday = null;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'typeOfAbsence')]
    private Collection $calendars;

    #[ORM\Column(nullable: true)]
    private ?bool $hasToBeValidated = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBankHoliday = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isWorkingDay = null;

    #[ORM\ManyToOne(inversedBy: 'typeOfAbsences')]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $shortTitle = null;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
        parent::__construct();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }


    public function isIsHoliday(): ?bool
    {
        return $this->isHoliday;
    }

    public function setIsHoliday(?bool $isHoliday): static
    {
        $this->isHoliday = $isHoliday;

        return $this;
    }

    public function isIsTimeHoliday(): ?bool
    {
        return $this->isTimeHoliday;
    }

    public function setIsTimeHoliday(?bool $isTimeHoliday): static
    {
        $this->isTimeHoliday = $isTimeHoliday;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->setTypeOfAbsence($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getTypeOfAbsence() === $this) {
                $calendar->setTypeOfAbsence(null);
            }
        }

        return $this;
    }

    public function isHasToBeValidated(): ?bool
    {
        return $this->hasToBeValidated;
    }

    public function setHasToBeValidated(?bool $hasToBeValidated): static
    {
        $this->hasToBeValidated = $hasToBeValidated;

        return $this;
    }

    public function isIsBankHoliday(): ?bool
    {
        return $this->isBankHoliday;
    }

    public function setIsBankHoliday(?bool $isBankHoliday): static
    {
        $this->isBankHoliday = $isBankHoliday;

        return $this;
    }

    public function isIsWorkingDay(): ?bool
    {
        return $this->isWorkingDay;
    }

    public function setIsWorkingDay(?bool $isWOrkingDay): static
    {
        $this->isWorkingDay = $isWOrkingDay;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    public function setShortTitle(?string $shortTitle): static
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }
}
