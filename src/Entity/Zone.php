<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\User\Role;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/zones",
        ],
        "post" => [
            "path" => "/admin/zones",
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/zones/{id}",
        ],
        "put" => [
            "path" => "/admin/zones/{id}",
        ],
        "patch" => [
            "path" => "/admin/zones/{id}",
        ],
        "delete" => [
            "path" => "/admin/zones/{id}",
        ],
    ],
    attributes: [
        "order" => ["type" => "DESC", "rangeStart" => "ASC"],
        "pagination_items_per_page" => 25,
        "pagination_client_items_per_page" => true,
    ],
)]
#[ORM\Entity]
class Zone
{
    use IdentifiableEntityTrait;

    #[ORM\Column(type: "string")]
    private string $name = '';

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $hint = null;

    #[ORM\Column(type: "float")]
    private ?float $rangeStart = 0.0;

    #[ORM\Column(type: "float")]
    private ?float $rangeEnd = 0.0;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $priority = null;

    #[ORM\Column(type: "boolean")]
    private bool $active = false;

    #[ORM\Column(type: "user.role", nullable: true)]
    #[Enum(Role::class)]
    private ?Role $type = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setHint(?string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function getRangeStart(): ?float
    {
        return $this->rangeStart;
    }

    public function setRangeStart(?float $rangeStart): self
    {
        $this->rangeStart = $rangeStart;

        return $this;
    }

    public function getRangeEnd(): ?float
    {
        return $this->rangeEnd;
    }

    public function setRangeEnd(?float $rangeEnd): self
    {
        $this->rangeEnd = $rangeEnd;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

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
