<?php declare(strict_types=1);

namespace App\Object\Scoring;

class ScoringOutput
{
    private iterable $evaluations = [];
    private float $score = 0.0;
    private ?string $userId = null;

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getEvaluations(): array
    {
        return $this->evaluations;
    }

    /**
     * @param EvaluationOutput[] $evaluations
     * @return $this
     */
    public function setEvaluations(array $evaluations): self
    {
        $this->evaluations = $evaluations;
        return $this;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;
        return $this;
    }
}
