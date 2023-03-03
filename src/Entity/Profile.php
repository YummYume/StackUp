<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProfileRepository;
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

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[UniqueEntity(
    fields: ['username'],
    message: 'profile.username.unique',
    errorPath: 'username',
    ignoreNull: false,
    repositoryMethod: 'sluggifyAndFind'
)]
class Profile
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'profile.username.not_blank')]
    #[Assert\Regex(pattern: '/^[A-zÀ-ú\d ]{2,50}$/', message: 'profile.username.invalid')]
    #[Groups('searchable')]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Gedmo\Slug(fields: ['username'])]
    #[Groups('searchable')]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2500, maxMessage: 'profile.description.max_length')]
    #[Groups('searchable')]
    private ?string $description = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Assert\Valid]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    #[Groups('searchable')]
    private ?ProfilePicture $picture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(https:\/\/)?github\.com\/[a-zA-Z0-9_]{1,50}$/',
        message: 'profile.github_link.invalid'
    )]
    #[Assert\Url(message: 'profile.github_link.not_url')]
    private ?string $githubLink = null;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Vote::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $votes;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Stack::class, orphanRemoval: true)]
    private Collection $stacks;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->stacks = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPicture(): ?ProfilePicture
    {
        return $this->picture;
    }

    public function setPicture(?ProfilePicture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    #[Groups('searchable')]
    public function isIndexable(): bool
    {
        return $this->user?->isVerified();
    }

    public function getGithubLink(): ?string
    {
        return $this->githubLink;
    }

    public function setGithubLink(?string $githubLink): self
    {
        $this->githubLink = $githubLink;

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setProfile($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getProfile() === $this) {
                $vote->setProfile(null);
            }
        }

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
            $stack->setProfile($this);
        }

        return $this;
    }

    public function removeStack(Stack $stack): self
    {
        if ($this->stacks->removeElement($stack)) {
            // set the owning side to null (unless already changed)
            if ($stack->getProfile() === $this) {
                $stack->setProfile(null);
            }
        }

        return $this;
    }
}
