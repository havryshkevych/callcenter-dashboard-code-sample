<?php declare(strict_types=1);

namespace App\Object\KnowledgeScoring;

use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class KnowledgeScoringInput
{
    private ?string $name;
    private ?User $user;
    private ?DateTime $date;
    private null|File|UploadedFile $screenshot;
    private ?float $result = 0.0;
    private ?float $coefficient = 0.0;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
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

    public function getScreenshot(): null|File|UploadedFile
    {
        return $this->screenshot;
    }

    public function setScreenshot(null|File|UploadedFile $screenshot): self
    {
        $this->screenshot = $screenshot;
        return $this;
    }

    public function getResult(): ?float
    {
        return $this->result;
    }

    public function setResult(?float $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function getCoefficient(): ?float
    {
        return $this->coefficient;
    }

    public function setCoefficient(?float $coefficient): self
    {
        $this->coefficient = $coefficient;
        return $this;
    }
}