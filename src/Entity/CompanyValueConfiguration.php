<?php

namespace App\Entity;

use App\Repository\CompanyValueConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyValueConfigurationRepository::class)]
class CompanyValueConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'companyValueConfigurations')]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'companyValueConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModuleConfiguration $configuration = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getConfiguration(): ?ModuleConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(?ModuleConfiguration $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }
}
