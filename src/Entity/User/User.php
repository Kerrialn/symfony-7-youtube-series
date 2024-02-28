<?php

namespace App\Entity\User;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Enum\EventParticipantTypeEnum;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'owner')]
    private Collection $events;

    #[ORM\OneToMany(targetEntity: EventParticipant::class, mappedBy: 'owner')]
    private Collection $sentParticipations;

    #[ORM\OneToMany(targetEntity: EventParticipant::class, mappedBy: 'target')]
    private Collection $reciviedParticipations;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->sentParticipations = new ArrayCollection();
        $this->reciviedParticipations = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOwner($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getOwner() === $this) {
                $event->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventParticipant>
     */
    public function getSentParticipations(): Collection
    {
        return $this->sentParticipations;
    }

    public function addSentParticipation(EventParticipant $eventParticipation): static
    {
        if (!$this->sentParticipations->contains($eventParticipation)) {
            $this->sentParticipations->add($eventParticipation);
            $eventParticipation->setOwner($this);
        }

        return $this;
    }

    public function removeSentParticipations(EventParticipant $eventParticipation): static
    {
        if ($this->sentParticipations->removeElement($eventParticipation)) {
            // set the owning side to null (unless already changed)
            if ($eventParticipation->getOwner() === $this) {
                $eventParticipation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventParticipant>
     */
    public function getReciviedParticipations(): Collection
    {
        return $this->reciviedParticipations;
    }

    public function addReciviedParticipation(EventParticipant $eventParticipant): static
    {
        if (!$this->reciviedParticipations->contains($eventParticipant)) {
            $this->reciviedParticipations->add($eventParticipant);
            $eventParticipant->setTarget($this);
        }

        return $this;
    }

    public function removeReciviedParticipation(EventParticipant $eventParticipant): static
    {
        if ($this->reciviedParticipations->removeElement($eventParticipant)) {
            // set the owning side to null (unless already changed)
            if ($eventParticipant->getTarget() === $this) {
                $eventParticipant->setTarget(null);
            }
        }

        return $this;
    }


    public function getFutureEvents() : ArrayCollection
    {
        return $this->events->filter(fn(Event $event) : bool => new DateTimeImmutable() < $event->getStartAt());
    }

}
