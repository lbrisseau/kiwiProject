<?php

namespace App\Entity;

use App\Repository\AnimationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnimationRepository::class)
 */
class Animation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carouselMessage1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carouselMessage2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carouselMessage3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carouselMessage4;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarouselMessage1(): ?string
    {
        return $this->carouselMessage1;
    }

    public function setCarouselMessage1(?string $carouselMessage1): self
    {
        $this->carouselMessage1 = $carouselMessage1;

        return $this;
    }

    public function getCarouselMessage2(): ?string
    {
        return $this->carouselMessage2;
    }

    public function setCarouselMessage2(?string $carouselMessage2): self
    {
        $this->carouselMessage2 = $carouselMessage2;

        return $this;
    }

    public function getCarouselMessage3(): ?string
    {
        return $this->carouselMessage3;
    }

    public function setCarouselMessage3(?string $carouselMessage3): self
    {
        $this->carouselMessage3 = $carouselMessage3;

        return $this;
    }

    public function getCarouselMessage4(): ?string
    {
        return $this->carouselMessage4;
    }

    public function setCarouselMessage4(?string $carouselMessage4): self
    {
        $this->carouselMessage4 = $carouselMessage4;

        return $this;
    }
}
