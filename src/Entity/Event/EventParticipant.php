<?php

namespace App\Entity\Event;

use App\Entity\User\User;
use App\Enum\EventParticipantTypeEnum;
use App\Repository\Event\EventParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventParticipantRepository::class)]
class EventParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventParticipants')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'sentParticipations')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'reciviedParticipations')]
    private ?User $target = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, enumType: EventParticipantTypeEnum::class)]
    private null|EventParticipantTypeEnum $type = EventParticipantTypeEnum::EVENT_PARTICIPANT;


    /**
     * @param Event|null $event
     * @param User|null $owner
     * @param EventParticipantTypeEnum|null $type
     */
    public function __construct(
        null|Event                    $event = null,
        null|User                     $owner = null,
        null|User                     $target = null,
        null|EventParticipantTypeEnum $type = null
    )
    {
        $this->event = $event;
        $this->owner = $owner;
        $this->target = $owner;
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): null|EventParticipantTypeEnum
    {
        return $this->type;
    }

    public function setType(null|EventParticipantTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): static
    {
        $this->target = $target;

        return $this;
    }
}
