<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\DatesEntityTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\ApiPlatform\UuidGenerator;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/scorings",
        ],
        "post" => [
            "path" => "/admin/scorings",
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/scorings/{id}",
        ],
        "put" => [
            "path" => "/admin/scorings/{id}",
        ],
        "patch" => [
            "path" => "/admin/scorings/{id}",
        ],
        "delete" => [
            "path" => "/admin/scorings/{id}",
        ],
    ],
    denormalizationContext: ["groups" => ["scoring:write"], "swagger_definition_name" => "Write"], normalizationContext: ["groups" => ["scoring:read"], "swagger_definition_name" => "Read"]
)]
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ["id" => "exact", "user" => "exact"])]
#[ApiFilter(DateFilter::class, properties: ["createdAt"])]
class Scoring
{
    use DatesEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(["scoring:read"])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Dialog::class, cascade: ["persist"], inversedBy: "scoring")]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    #[Groups(["scoring:read", "scoring:write"])]
    private Dialog $dialog;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    #[Groups(["scoring:read", "scoring:write"])]
    private ?User $user = null;

    /**
     * @var Collection<Evaluation>|Evaluation[]
     */
    #[ORM\OneToMany(mappedBy: "scoring", targetEntity: Evaluation::class, cascade: ["persist"], orphanRemoval: true)]
    #[Groups(["scoring:read", "scoring:write"])]
    #[Assert\Valid]
    private Collection $evaluations;

    #[ORM\Column(type: 'float')]
    private float $score = 0.0;

    #[Pure] public function __construct()
    {
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDialog(): ?Dialog
    {
        return $this->dialog;
    }

    public function setDialog(Dialog $dialog): self
    {
        $this->dialog = $dialog;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): ?self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<Evaluation>|Evaluation[]
     */
    public function getEvaluations(): Collection|array
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setScoring($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            if ($evaluation->getScoring() === $this) {
                $evaluation->setScoring(null);
            }
        }

        return $this;
    }

    #[Groups(["scoring:read"])]
    public function getScore(): ?float
    {
        return array_sum(
            $this->getEvaluations()->map(fn(Evaluation $ev) => $ev->getValue())->getValues()
        );
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }
}
