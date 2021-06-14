<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $defaultStartMemberSubs;

    /**
     * @ORM\Column(type="integer")
     */
    private $defaultStartAllSubs;

    /**
     * @ORM\Column(type="integer")
     */
    private $defaultEndSubs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefaultStartMemberSubs(): ?int
    {
        return $this->defaultStartMemberSubs;
    }

    public function setDefaultStartMemberSubs(int $defaultStartMemberSubs): self
    {
        $this->defaultStartMemberSubs = $defaultStartMemberSubs;

        return $this;
    }

    public function getDefaultStartAllSubs(): ?int
    {
        return $this->defaultStartAllSubs;
    }

    public function setDefaultStartAllSubs(int $defaultStartAllSubs): self
    {
        $this->defaultStartAllSubs = $defaultStartAllSubs;

        return $this;
    }

    public function getDefaultEndSubs(): ?int
    {
        return $this->defaultEndSubs;
    }

    public function setDefaultEndSubs(int $defaultEndSubs): self
    {
        $this->defaultEndSubs = $defaultEndSubs;

        return $this;
    }
}
