<?php

namespace App\Entity;

use App\Repository\ModuleConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleConfigurationRepository::class)]
class ModuleConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $module = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;


    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ModuleConfigurationValue>
     */
    #[ORM\OneToMany(targetEntity: ModuleConfigurationValue::class, mappedBy: 'moduleConfiguration')]
    private Collection $moduleConfigurationValues;

    #[ORM\Column(length: 255)]
    private ?string $targetEntity = null;

    public function __construct()
    {
        $this->moduleConfigurationValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, ModuleConfigurationValue>
     */
    public function getModuleConfigurationValues(): Collection
    {
        return $this->moduleConfigurationValues;
    }

    public function addModuleConfigurationValue(ModuleConfigurationValue $moduleConfigurationValue): static
    {
        if (!$this->moduleConfigurationValues->contains($moduleConfigurationValue)) {
            $this->moduleConfigurationValues->add($moduleConfigurationValue);
            $moduleConfigurationValue->setModuleConfiguration($this);
        }

        return $this;
    }

    public function removeModuleConfigurationValue(ModuleConfigurationValue $moduleConfigurationValue): static
    {
        if ($this->moduleConfigurationValues->removeElement($moduleConfigurationValue)) {
            // set the owning side to null (unless already changed)
            if ($moduleConfigurationValue->getModuleConfiguration() === $this) {
                $moduleConfigurationValue->setModuleConfiguration(null);
            }
        }

        return $this;
    }

    public function getTargetEntity(): ?string
    {
        return $this->targetEntity;
    }

    public function setTargetEntity(string $targetEntity): static
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }
}
