<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
class Media extends TimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $filename = null;

    #[Vich\UploadableField(mapping: 'medias', fileNameProperty: 'filename')]
    private ?File $file = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commentable $commentable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theuid = null;

    public function getId(): ?int
    {
        return $this->id;
    }
   /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->theuid = uniqid();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getCommentable(): ?Commentable
    {
        return $this->commentable;
    }

    public function setCommentable(?Commentable $commentable): static
    {
        $this->commentable = $commentable;

        return $this;
    }

    public function getTheuid(): ?string
    {
        return $this->theuid;
    }

    public function setTheuid(?string $theuid): static
    {
        $this->theuid = $theuid;

        return $this;
    }
}
