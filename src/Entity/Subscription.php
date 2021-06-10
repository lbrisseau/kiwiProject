<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $subsDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validationState;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

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

    public function getSubsDate(): ?\DateTimeInterface
    {
        return $this->subsDate;
    }

    public function setSubsDate(\DateTimeInterface $subsDate): self
    {
        $this->subsDate = $subsDate;

        return $this;
    }

    public function getValidationState(): ?bool
    {
        return $this->validationState;
    }

    public function setValidationState(bool $validationState): self
    {
        $this->validationState = $validationState;

        return $this;
    }
}
