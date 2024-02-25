<?php

namespace App\Entity;

use App\Repository\TypeOfAbsenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeOfAbsenceRepository::class)]
class TypeOfAbsence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isHoliday = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isTimeHoliday = null;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'typeOfAbsence')]
    private Collection $calendars;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

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
}
