<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\Dialog\Type;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/criterias",
        ],
        "post" => [
            "path" => "/admin/criterias",
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/criterias/{id}",
        ],
        "put" => [
            "path" => "/admin/criterias/{id}",
        ],
        "patch" => [
            "path" => "/admin/criterias/{id}",
        ],
        "delete" => [
            "path" => "/admin/criterias/{id}",
        ],
    ],
    shortName: "Criterias",
    attributes: ["order" => ["sort" => "ASC"]]
)]
#[ORM\Entity]
#[ApiFilter(SearchFilter::class, properties: ["type" => "exact"])]
#[ApiFilter(BooleanFilter::class, properties: ["active"])]
#[ApiFilter(OrderFilter::class, properties: ['sort' => 'ASC', 'title' => 'ASC'])]
class EvaluationCriteria
{
    use IdentifiableEntityTrait;

    #[ORM\Column(type: "dialog.type")]
    #[Enum(Type::class)]
    private ?Type $type = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $sort = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => false])]
    private bool $active = false;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $max = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => false])]
    private bool $critical = false;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): EvaluationCriteria
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): EvaluationCriteria
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): EvaluationCriteria
    {
        $this->type = $type;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

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

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function setCritical(bool $critical): self
    {
        $this->critical = $critical;

        return $this;
    }
}
