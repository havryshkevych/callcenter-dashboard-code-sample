<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\EntityTrait\DatesEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\ApiPlatform\UuidGenerator;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/evaluations",
        ],
        "post" => [
            "path" => "/admin/evaluations",
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/evaluations/{id}",
        ],
        "put" => [
            "path" => "/admin/evaluations/{id}",
        ],
        "patch" => [
            "path" => "/admin/evaluations/{id}",
        ],
        "delete" => [
            "path" => "/admin/evaluations/{id}",
        ],
    ],
    attributes: ["order" => ["createdAt" => "DESC"], "pagination_items_per_page" => 10],
    denormalizationContext: ["groups" => ["evaluation:write"], "swagger_definition_name" => "Write"],
    normalizationContext: ["groups" => ["evaluation:read"], "swagger_definition_name" => "Read"]
)]
#[ORM\Entity, ORM\HasLifecycleCallbacks]
class Evaluation
{
    use DatesEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(["evaluation:read"])]
    protected ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Scoring::class, inversedBy: "evaluations")]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    #[Groups(["evaluation:read", "evaluation:write"])]
    #[Assert\Valid]
    private Scoring $scoring;

    #[ORM\ManyToOne(targetEntity: EvaluationCriteria::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["evaluation:read", "evaluation:write", "scoring:read", "scoring:write"])]
    private EvaluationCriteria $criteria;

    #[ORM\Column(type: "float")]
    #[Groups(["evaluation:read", "evaluation:write", "scoring:read", "scoring:write"])]
    private float $value = 0.0;

    #[ORM\Column(type: "text")]
    #[Groups(["evaluation:read", "evaluation:write", "scoring:read", "scoring:write"])]
    private ?string $comment = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getScoring(): Scoring
    {
        return $this->scoring;
    }

    public function setScoring(Scoring $scoring): self
    {
        $this->scoring = $scoring;

        return $this;
    }

    public function getCriteria(): EvaluationCriteria
    {
        return $this->criteria;
    }

    public function setCriteria(EvaluationCriteria $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    #[ORM\PreUpdate, ORM\PrePersist, ORM\PostUpdate]
    public function updateDate(): void
    {
        $this->getScoring()->setScore($this->getScoring()->getScore());
    }
}
