<?php declare(strict_types=1);

namespace App\Object\Scoring;

class EvaluationOutput
{
    protected ?string $id = null;
    private CriteriaOutput $criteria;
    private float $value = 0.0;
    private ?string $comment = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCriteria(): CriteriaOutput
    {
        return $this->criteria;
    }

    public function setCriteria(CriteriaOutput $criteria): self
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
}
