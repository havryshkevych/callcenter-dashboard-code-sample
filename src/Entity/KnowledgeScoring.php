<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\DatesEntityTrait;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Object\KnowledgeScoring\KnowledgeScoringInput;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;


#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/knowledge-scorings",
        ],
        "post" => [
            "path" => "/admin/knowledge-scorings",
            "input" => KnowledgeScoringInput::class,
            "input_formats" => [
                "multipart" => ['multipart/form-data'],
            ],
        ],
        "clientList" => [
            "method" => Request::METHOD_GET,
            "path" => "/knowledge-scorings"
        ]
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/knowledge-scorings/{id}",
        ],
        "put" => [
            "path" => "/admin/knowledge-scorings/{id}",
        ],
        "patch" => [
            "path" => "/admin/knowledge-scorings/{id}",
        ],
        "delete" => [
            "path" => "/admin/knowledge-scorings/{id}",
        ],
    ],
)]
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ["user" => "exact"])]
#[ApiFilter(DateFilter::class, properties: ["createdAt"])]
class KnowledgeScoring
{
    use IdentifiableEntityTrait, DatesEntityTrait;

    #[ORM\Column(type: "string")]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "knowledgeScorings")]
    private ?User $user = null;

    #[ORM\Column(type: "datetime")]
    private ?DateTime $date = null;

    #[ORM\Column(type: 'string')]
    private ?string $screenshot = null;

    #[ORM\Column(type: 'float')]
    private float $result = 0.0;

    #[ORM\Column(type: 'float')]
    private float $coefficient = 0.0;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
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

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): self
    {
        $this->screenshot = $screenshot;
        return $this;
    }

    public function getResult(): float
    {
        return $this->result;
    }

    public function setResult(float $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function getCoefficient(): float
    {
        return $this->coefficient;
    }

    public function setCoefficient(float $coefficient): self
    {
        $this->coefficient = $coefficient;
        return $this;
    }
}
