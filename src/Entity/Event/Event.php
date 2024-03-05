<?php

namespace App\Entity\Event;

use App\Entity\User\User;
use App\Repository\Event\EventRepository;
use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonImmutableType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: EventParticipant::class, mappedBy: 'event')]
    private Collection $eventParticipants;

    public function __construct(
        null|string            $title = null,
        null|DateTimeImmutable $startAt = null,
        null|DateTimeImmutable $endAt = null,
        null|User              $owner = null
    )
    {
        $this->title = $title;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->owner = $owner;
        $this->createdAt = new DateTimeImmutable();
        $this->eventParticipants = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, EventParticipant>
     */
    public function getEventParticipants(): Collection
    {
        return $this->eventParticipants;
    }

    public function addEventParticipant(EventParticipant $eventParticipant): static
    {
        if (!$this->eventParticipants->contains($eventParticipant)) {
            $this->eventParticipants->add($eventParticipant);
            $eventParticipant->setEvent($this);
        }

        return $this;
    }

    public function removeEventParticipant(EventParticipant $eventParticipant): static
    {
        if ($this->eventParticipants->removeElement($eventParticipant)) {
            // set the owning side to null (unless already changed)
            if ($eventParticipant->getEvent() === $this) {
                $eventParticipant->setEvent(null);
            }
        }

        return $this;
    }

    public function getIsAttending(User $user): bool
    {
        return $this->eventParticipants->exists(fn(int $key, EventParticipant $eventParticipant): bool => $eventParticipant->getTarget() === $user);
    }

    public function getAttendingUser(User $user): null|EventParticipant
    {
        return $this->eventParticipants->findFirst(fn(int $key, EventParticipant $eventParticipant): bool => $eventParticipant->getTarget() === $user);
    }

    public function getParticipantsAddedInLast24Hours() : ArrayCollection
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $now = new CarbonImmutable();

        $criteria->andWhere(
            $expr->gt('createdAt', $now->subHours(24))
        );

        return $this->eventParticipants->matching($criteria);
    }
}
