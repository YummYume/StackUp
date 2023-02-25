<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\StackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StackRepository::class)]
#[UniqueEntity(
    fields: ['name', 'profile'],
    message: 'stack.name.unique',
    errorPath: 'name',
    ignoreNull: false,
)]
class Stack
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Regex(pattern: '/^[A-zÀ-ú\d ]{2,50}$/', message: 'stack.name.invalid')]
    #[Groups('searchable')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2500, maxMessage: 'stack.description.max_length')]
    #[Groups('searchable')]
    private ?string $description = null;

    #[ORM\Column(length: 150)]
    #[Gedmo\Slug(fields: ['name'])]
    #[Groups('searchable')]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Tech::class, inversedBy: 'stacks')]
    #[Assert\Count(
        min: 1,
        max: 50,
        minMessage: 'stack.techs.min_count',
        maxMessage: 'stack.techs.max_count',
    )]
    private Collection $techs;

    #[ORM\ManyToOne(inversedBy: 'stacks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
        }

        return $this;
    }

    public function removeTech(Tech $tech): self
    {
        $this->techs->removeElement($tech);

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getCategoriesWithTechs(bool $noDuplicates = false): array
    {
        $categories = [];

        foreach ($this->techs as $tech) {
            /** @var Collection<Category> */
            $techCategories = $tech->getCategories();

            foreach ($techCategories as $i => $category) {
                $categories[$category->getName()][] = $tech;

                if (0 === $i && $noDuplicates) {
                    continue;
                }
            }
        }

        return $categories;
    }
}
