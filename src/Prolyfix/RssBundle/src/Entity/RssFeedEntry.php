<?php

namespace Prolyfix\RssBundle\Entity;

use App\Repository\RssFeedEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RssFeedEntryRepository::class)]
class RssFeedEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 511, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'rssFeedEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RssFeedList $rssFeedList = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getRssFeedList(): ?RssFeedList
    {
        return $this->rssFeedList;
    }

    public function setRssFeedList(?RssFeedList $rssFeedList): static
    {
        $this->rssFeedList = $rssFeedList;

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
}
