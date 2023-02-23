<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(
    fields: ['name'],
    message: 'category.name.unique',
    errorPath: 'name',
    ignoreNull: false,
)]
class Category
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Regex(pattern: '/^[A-zÀ-ú\d ]{2,50}$/', message: 'category.name.invalid')]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Tech::class, mappedBy: 'categories')]
    private Collection $techs;

    #[ORM\Column(length: 150)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    public function __construct()
    {
        $this->techs = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tech>
     */
    public function getTechs(): Collection
    {
        return $this->techs;
    }

    public function addTech(Tech $tech): self
    {
        if (!$this->techs->contains($tech)) {
            $this->techs->add($tech);
            $tech->addCategory($this);
        }

        return $this;
    }

    public function removeTech(Tech $tech): self
    {
        if ($this->techs->removeElement($tech)) {
            $tech->removeCategory($this);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isOfficial(): bool
    {
        foreach ($this->techs as $tech) {
            if ($tech->isOfficial()) {
                return true;
            }
        }

        return false;
    }
}
