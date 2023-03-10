<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\FilePropertiesTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProfilePictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProfilePictureRepository::class)]
#[Vich\Uploadable]
class TechPicture implements ImageUploadInterface
{
    use BlameableTrait;
    use FilePropertiesTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[Vich\UploadableField(
        mapping: 'tech_picture',
        fileNameProperty: 'fileName',
        size: 'size',
        mimeType: 'mimeType',
        originalName: 'originalName',
        dimensions: 'dimensions'
    )]
    #[Assert\Image(
        maxSize: '2M',
        maxSizeMessage: 'tech_picture.file.max_size',
        mimeTypes: ['image/png', 'image/jpeg', 'image/gif'],
        mimeTypesMessage: 'tech_picture.file.mime_types',
        detectCorrupted: true,
        corruptedMessage: 'tech_picture.file.corrupted',
        sizeNotDetectedMessage: 'tech_picture.file.size_not_detected'
    )]
    private ?File $file = null;

    #[ORM\OneToOne(mappedBy: 'picture')]
    private ?Tech $tech = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file = null): static
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getTech(): ?Tech
    {
        return $this->tech;
    }

    public function setTech(Tech $tech): self
    {
        $this->tech = $tech;

        return $this;
    }
}
