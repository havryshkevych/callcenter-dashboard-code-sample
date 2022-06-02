<?php declare(strict_types=1);

namespace App\Object\User;

use App\Entity\Zone;

class SupervisorOutput
{
    private ?string $id = null;
    private ?string $name = null;
    private ?float $activeTime = null;
    private ?float $scoringRatio = null;
    private ?Zone $zone = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
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

    public function getActiveTime(): ?float
    {
        return $this->activeTime;
    }

    public function setActiveTime(?float $activeTime): self
    {
        $this->activeTime = $activeTime;

        return $this;
    }

    public function getScoringRatio(): ?float
    {
        return $this->scoringRatio;
    }

    public function setScoringRatio(?float $scoringRatio): self
    {
        $this->scoringRatio = $scoringRatio;

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }
}
