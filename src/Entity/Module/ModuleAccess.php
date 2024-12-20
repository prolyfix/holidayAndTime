<?php

namespace App\Entity\Module;


use App\Repository\ModuleAccessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleAccessRepository::class)]
class ModuleAccess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleAccesses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tenantClass = null;

    #[ORM\Column]
    private ?int $tenantId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getTenantClass(): ?string
    {
        return $this->tenantClass;
    }

    public function setTenantClass(?string $tenantClass): static
    {
        $this->tenantClass = $tenantClass;

        return $this;
    }

    public function getTenantId(): ?int
    {
        return $this->tenantId;
    }

    public function setTenantId(int $tenantId): static
    {
        $this->tenantId = $tenantId;

        return $this;
    }
}
