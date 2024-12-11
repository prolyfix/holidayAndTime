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

    /**
     * @var Collection<int, CompanyValueConfiguration>
     */
    #[ORM\OneToMany(targetEntity: CompanyValueConfiguration::class, mappedBy: 'configuration')]
    private Collection $companyValueConfigurations;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->companyValueConfigurations = new ArrayCollection();
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

    /**
     * @return Collection<int, CompanyValueConfiguration>
     */
    public function getCompanyValueConfigurations(): Collection
    {
        return $this->companyValueConfigurations;
    }

    public function addCompanyValueConfiguration(CompanyValueConfiguration $companyValueConfiguration): static
    {
        if (!$this->companyValueConfigurations->contains($companyValueConfiguration)) {
            $this->companyValueConfigurations->add($companyValueConfiguration);
            $companyValueConfiguration->setConfiguration($this);
        }

        return $this;
    }

    public function removeCompanyValueConfiguration(CompanyValueConfiguration $companyValueConfiguration): static
    {
        if ($this->companyValueConfigurations->removeElement($companyValueConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($companyValueConfiguration->getConfiguration() === $this) {
                $companyValueConfiguration->setConfiguration(null);
            }
        }

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
}
