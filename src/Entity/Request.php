<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\RequestStatusEnum;
use App\Repository\RequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100, enumType: RequestStatusEnum::class, nullable: false)]
    private RequestStatusEnum $status = RequestStatusEnum::Pending;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update', field: 'status')]
    private ?\DateTimeInterface $lastChangedAt = null;

    #[ORM\OneToMany(mappedBy: 'request', targetEntity: Vote::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $votes;

    #[ORM\OneToOne(mappedBy: 'request', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?Tech $tech = null;

    #[ORM\Column]
    private bool $created = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update', field: 'created')]
    private ?\DateTimeInterface $submittedAt = null;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getStatus(): RequestStatusEnum
    {
        return $this->status;
    }

    public function setStatus(RequestStatusEnum $status): self
    {
        $this->status = $status;
        // Update the tech for Meilisearch
        $this->tech?->setUpdatedAt(new \DateTime());

        return $this;
    }

    public function getLastChangedAt(): ?\DateTimeInterface
    {
        return $this->lastChangedAt;
    }

    public function setLastChangedAt(\DateTimeInterface $lastChangedAt): self
    {
        $this->lastChangedAt = $lastChangedAt;

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
            $vote->setRequest($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getRequest() === $this) {
                $vote->setRequest(null);
            }
        }

        return $this;
    }

    public function getTech(): ?Tech
    {
        return $this->tech;
    }

    public function setTech(Tech $tech): self
    {
        // set the owning side of the relation if necessary
        if ($tech->getRequest() !== $this) {
            $tech->setRequest($this);
        }

        $this->tech = $tech;

        return $this;
    }

    public function isCreated(): bool
    {
        return $this->created;
    }

    public function setCreated(bool $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    public function isOfficial(): bool
    {
        return $this->created && RequestStatusEnum::Accepted === $this->status;
    }

    public function getUpvotes(): Collection
    {
        return $this->votes->filter(static fn (Vote $vote): bool => $vote->isUpvote());
    }

    public function getDownvotes(): Collection
    {
        return $this->votes->filter(static fn (Vote $vote): bool => !$vote->isUpvote());
    }

    public function getVoteCount(): int
    {
        return array_reduce($this->votes->toArray(), static fn (int $total, Vote $vote): int => $vote->isUpvote() ? ($total + 1) : ($total - 1), 0);
    }

    public function getDownAndUpVotes(): array
    {
        $votes = [
            'upvotes' => 0,
            'downvotes' => 0,
        ];

        foreach ($this->votes as $vote) {
            ++$votes[$vote->isUpvote() ? 'upvotes' : 'downvotes'];
        }

        return $votes;
    }
}
