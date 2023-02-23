<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\TechTypeEnum;
use App\Repository\TechRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TechRepository::class)]
#[UniqueEntity(
    fields: ['name'],
    message: 'tech.name.unique',
    errorPath: 'name',
    ignoreNull: false,
    repositoryMethod: 'findCreatedTech'
)]
class Tech
{
    use BlameableTrait;
    use TimestampableTrait;

    public const LINK_GITHUB = 'github';
    public const LINK_GITLAB = 'gitlab';
    public const LINK_NPM_OR_YARN = 'npm_or_yarn';
    public const LINK_PACKAGIST = 'packagist';
    public const LINK_CRATES = 'crates';
    public const LINK_PKG = 'pkg';
    public const LINK_OTHER = 'other';

    public const ALLOWED_LINKS = [
        self::LINK_GITHUB,
        self::LINK_GITLAB,
        self::LINK_NPM_OR_YARN,
        self::LINK_PACKAGIST,
        self::LINK_CRATES,
        self::LINK_PKG,
        self::LINK_OTHER,
    ];

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Regex(pattern: '/^[A-zÀ-ú\d ]{2,50}$/', message: 'tech.name.invalid')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2500, maxMessage: 'tech.description.max_length')]
    private ?string $description = null;

    #[ORM\Column(length: 100, enumType: TechTypeEnum::class)]
    private TechTypeEnum $type = TechTypeEnum::Language;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'techs')]
    #[Assert\Count(
        min: 1,
        max: 3,
        minMessage: 'tech.categories.min_count',
        maxMessage: 'tech.categories.max_count',
    )]
    private Collection $categories;

    #[ORM\Column(length: 150)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\All([
        new Assert\Url(),
    ])]
    #[Assert\Collection(
        fields: [
            self::LINK_GITHUB => new Assert\Regex(
                pattern: '/^(https:\/\/)?github\.com\/[a-zA-Z0-9_-]{1,50}\/[a-zA-Z0-9_-]{1,50}$/',
                message: 'tech.link.github.invalid'
            ),
            self::LINK_GITLAB => new Assert\Regex(
                pattern: '/^(https:\/\/)?gitlab\.com\/[a-zA-Z0-9_-]{1,50}\/[a-zA-Z0-9_-]{1,50}$/',
                message: 'tech.link.gitlab.invalid'
            ),
            self::LINK_NPM_OR_YARN => new Assert\AtLeastOneOf(
                [
                    new Assert\Regex(pattern: '/^(https:\/\/)?www\.npmjs\.com\/package\/[a-zA-Z0-9_-]{1,50}$/'),
                    new Assert\Regex(pattern: '/^(https:\/\/)?yarnpkg\.com\/package\/[a-zA-Z0-9_-]{1,50}$/'),
                ],
                includeInternalMessages: false,
                message: 'tech.link.npm_or_yarn.invalid'
            ),
            self::LINK_PACKAGIST => new Assert\Regex(
                pattern: '/^(https:\/\/)?packagist\.org\/packages\/[a-zA-Z0-9_-]{1,50}\/[a-zA-Z0-9_-]{1,50}$/',
                message: 'tech.link.packagist.invalid'
            ),
            self::LINK_CRATES => new Assert\Regex(
                pattern: '/^(https:\/\/)?crates\.io\/crates\/[a-zA-Z0-9_-]{1,50}$/',
                message: 'tech.link.crates.invalid'
            ),
            self::LINK_PKG => new Assert\Regex(
                pattern: '/^(https:\/\/)?pkg\.go\.dev\/[a-zA-Z0-9_-]{1,50}$/',
                message: 'tech.link.pkg.invalid'
            ),
            self::LINK_OTHER => new Assert\Regex(
                pattern: '/^https:\/\//',
                message: 'tech.link.other.invalid'
            ),
        ],
        allowExtraFields: false,
        allowMissingFields: true,
    )]
    private array $links = [];

    #[ORM\OneToOne(mappedBy: 'tech', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?TechPicture $picture = null;

    #[ORM\OneToOne(inversedBy: 'tech', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Request $request = null;

    #[ORM\ManyToMany(targetEntity: Stack::class, mappedBy: 'techs')]
    private Collection $stacks;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'techs')]
    #[Assert\When(
        expression: 'this.getType().value === "library"',
        constraints: [
            new Assert\NotBlank(message: 'tech.depends_on.not_blank'),
        ],
    )]
    #[Assert\Expression(
        expression: 'value === null or value?.getRequest()?.getStatus()?.value === "accepted"',
        message: 'tech.depends_on.only_accepted',
    )]
    private ?self $dependsOn = null;

    #[ORM\OneToMany(mappedBy: 'dependsOn', targetEntity: self::class)]
    private Collection $techs;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->stacks = new ArrayCollection();
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

    public function getType(): TechTypeEnum
    {
        return $this->type;
    }

    public function setType(TechTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function getPicture(): ?TechPicture
    {
        return $this->picture;
    }

    public function setPicture(TechPicture $picture): self
    {
        // set the owning side of the relation if necessary
        if ($picture->getTech() !== $this) {
            $picture->setTech($this);
        }

        $this->picture = $picture;

        return $this;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Collection<int, Stack>
     */
    public function getStacks(): Collection
    {
        return $this->stacks;
    }

    public function addStack(Stack $stack): self
    {
        if (!$this->stacks->contains($stack)) {
            $this->stacks->add($stack);
            $stack->addTech($this);
        }

        return $this;
    }

    public function removeStack(Stack $stack): self
    {
        if ($this->stacks->removeElement($stack)) {
            $stack->removeTech($this);
        }

        return $this;
    }

    public function isOfficial(): bool
    {
        return $this->request->isOfficial();
    }

    public function getDependsOn(): ?self
    {
        return $this->dependsOn;
    }

    public function setDependsOn(?self $dependsOn): self
    {
        $this->dependsOn = $dependsOn;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getTechs(): Collection
    {
        return $this->techs;
    }

    public function addTech(self $tech): self
    {
        if (!$this->techs->contains($tech)) {
            $this->techs->add($tech);
            $tech->setDependsOn($this);
        }

        return $this;
    }

    public function removeTech(self $tech): self
    {
        if ($this->techs->removeElement($tech)) {
            // set the owning side to null (unless already changed)
            if ($tech->getDependsOn() === $this) {
                $tech->setDependsOn(null);
            }
        }

        return $this;
    }
}
