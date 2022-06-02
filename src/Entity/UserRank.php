<?php

namespace App\Entity;

use App\Entity\EntityTrait\DatesEntityTrait;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\User\Role;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class UserRank
{
    use IdentifiableEntityTrait, DatesEntityTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "ranks")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Zone::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Zone $zone;

    #[ORM\Column(type: "datetime")]
    private ?DateTime $date = null;

    #[ORM\Column(type: "integer")]
    private ?int $position = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $score = null;

    #[ORM\Column(type: "boolean")]
    private bool $trainee = false;

    #[ORM\Column(type: "user.role", nullable: true)]
    #[Enum(Role::class)]
    private ?Role $type = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getZone(): Zone
    {
        return $this->zone;
    }

    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): UserRank
    {
        $this->score = $score;
        return $this;
    }

    public function isTrainee(): bool
    {
        return $this->trainee;
    }

    public function setTrainee(bool $trainee): self
    {
        $this->trainee = $trainee;

        return $this;
    }

    public function getType(): ?Role
    {
        return $this->type;
    }

    public function setType(?Role $type): self
    {
        $this->type = $type;
        return $this;
    }
}
