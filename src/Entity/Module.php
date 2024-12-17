<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $class = null;


    /**
     * @var Collection<int, ModuleConfiguration>
     */
    #[ORM\OneToMany(targetEntity: ModuleConfiguration::class, mappedBy: 'module')]
    private Collection $moduleConfigurations;


    /**
     * @var Collection<int, ModuleAccess>
     */
    #[ORM\OneToMany(targetEntity: ModuleAccess::class, mappedBy: 'module')]
    private Collection $moduleAccesses;

    /**
     * @var Collection<int, ModuleRight>
     */
    #[ORM\OneToMany(targetEntity: ModuleRight::class, mappedBy: 'module')]
    private Collection $moduleRights;

    public function __construct()
    {
        $this->moduleConfigurations = new ArrayCollection();
        $this->moduleAccesses = new ArrayCollection();
        $this->moduleRights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

        return $this;
    }


    /**
     * @return Collection<int, ModuleConfiguration>
     */
    public function getModuleConfigurations(): Collection
    {
        return $this->moduleConfigurations;
    }

    public function addModuleConfiguration(ModuleConfiguration $moduleConfiguration): static
    {
        if (!$this->moduleConfigurations->contains($moduleConfiguration)) {
            $this->moduleConfigurations->add($moduleConfiguration);
            $moduleConfiguration->setModule($this);
        }

        return $this;
    }

    public function removeModuleConfiguration(ModuleConfiguration $moduleConfiguration): static
    {
        if ($this->moduleConfigurations->removeElement($moduleConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($moduleConfiguration->getModule() === $this) {
                $moduleConfiguration->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModuleAccess>
     */
    public function getModuleAccesses(): Collection
    {
        return $this->moduleAccesses;
    }

    public function addModuleAccess(ModuleAccess $moduleAccess): static
    {
        if (!$this->moduleAccesses->contains($moduleAccess)) {
            $this->moduleAccesses->add($moduleAccess);
            $moduleAccess->setModule($this);
        }

        return $this;
    }

    public function removeModuleAccess(ModuleAccess $moduleAccess): static
    {
        if ($this->moduleAccesses->removeElement($moduleAccess)) {
            // set the owning side to null (unless already changed)
            if ($moduleAccess->getModule() === $this) {
                $moduleAccess->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModuleRight>
     */
    public function getModuleRights(): Collection
    {
        return $this->moduleRights;
    }

    public function addModuleRight(ModuleRight $moduleRight): static
    {
        if (!$this->moduleRights->contains($moduleRight)) {
            $this->moduleRights->add($moduleRight);
            $moduleRight->setModule($this);
        }

        return $this;
    }

    public function removeModuleRight(ModuleRight $moduleRight): static
    {
        if ($this->moduleRights->removeElement($moduleRight)) {
            // set the owning side to null (unless already changed)
            if ($moduleRight->getModule() === $this) {
                $moduleRight->setModule(null);
            }
        }

        return $this;
    }
}
