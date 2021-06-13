<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $startMemberSubs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $startAllSubs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $endSubs;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="event")
     */
    private $subscriptions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleCalendarUrl;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStartMemberSubs(): ?int
    {
        return $this->startMemberSubs;
    }

    public function setStartMemberSubs(?int $startMemberSubs): self
    {
        $this->startMemberSubs = $startMemberSubs;

        return $this;
    }

    public function getStartAllSubs(): ?int
    {
        return $this->startAllSubs;
    }

    public function setStartAllSubs(?int $startAllSubs): self
    {
        $this->startAllSubs = $startAllSubs;

        return $this;
    }

    public function getEndSubs(): ?int
    {
        return $this->endSubs;
    }

    public function setEndSubs(?int $endSubs): self
    {
        $this->endSubs = $endSubs;

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setEvent($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getEvent() === $this) {
                $subscription->setEvent(null);
            }
        }

        return $this;
    }

    public function getGoogleCalendarUrl(): ?string
    {
        return $this->googleCalendarUrl;
    }

    public function setGoogleCalendarUrl(?string $googleCalendarUrl): self
    {
        $this->googleCalendarUrl = $googleCalendarUrl;

        return $this;
    }
}
