<?php

namespace App\Object\Dialog;

use App\Enum\Dialog\Type;
use App\Object\Scoring\ScoringOutput;
use App\Object\User\UserOutput;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

class DialogOutput
{
    private ?string $id = null;
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;
    private ?DateTime $date = null;
    private ?Type $type = null;
    private iterable $users = [];
    private iterable $scoring = [];
    private iterable $records = [];
    private iterable $recordsUrl = [];
    private ?int $duration = null;
    private ?int $firstAnswerSpeed = null;
    private ?int $averageSpeedAnswer = null;
    private ?bool $serviceLevelWarning = null;
    private ?bool $serviceLevelAverageAnswerSpeedWarning = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return DialogOutput
     */
    public function setId(?string $id): DialogOutput
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     * @return DialogOutput
     */
    public function setCreatedAt(?DateTime $createdAt): DialogOutput
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     * @return DialogOutput
     */
    public function setUpdatedAt(?DateTime $updatedAt): DialogOutput
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return DialogOutput
     */
    public function setDate(?DateTime $date): DialogOutput
    {
        $this->date = $date;
        return $this;
    }

    public function getScoring(): iterable
    {
        return $this->scoring;
    }

    public function setScoring(iterable $scoring): self
    {
        $this->scoring = $scoring;
        return $this;
    }

    /**
     * @return Type|null
     */
    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * @param Type|null $type
     * @return DialogOutput
     */
    public function setType(?Type $type): DialogOutput
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return iterable
     */
    public function getRecords(): iterable
    {
        return $this->records;
    }

    /**
     * @param array|iterable $records
     * @return DialogOutput
     */
    public function setRecords(iterable $records): DialogOutput
    {
        $this->records = $records;
        return $this;
    }

    public function getRecordsUrl(): iterable
    {
        return $this->recordsUrl;
    }

    public function setRecordsUrl(iterable $recordsUrl): self
    {
        $this->recordsUrl = $recordsUrl;
        return $this;
    }

    public function getUsers(): iterable
    {
        return $this->users;
    }

    public function setUsers(iterable $users): self
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     * @return DialogOutput
     */
    public function setDuration(?int $duration): DialogOutput
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFirstAnswerSpeed(): ?int
    {
        return $this->firstAnswerSpeed;
    }

    /**
     * @param int|null $firstAnswerSpeed
     * @return DialogOutput
     */
    public function setFirstAnswerSpeed(?int $firstAnswerSpeed): DialogOutput
    {
        $this->firstAnswerSpeed = $firstAnswerSpeed;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAverageSpeedAnswer(): ?int
    {
        return $this->averageSpeedAnswer;
    }

    /**
     * @param int|null $averageSpeedAnswer
     * @return DialogOutput
     */
    public function setAverageSpeedAnswer(?int $averageSpeedAnswer): DialogOutput
    {
        $this->averageSpeedAnswer = $averageSpeedAnswer;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getServiceLevelWarning(): ?bool
    {
        return $this->serviceLevelWarning;
    }

    /**
     * @param bool|null $serviceLevelWarning
     * @return DialogOutput
     */
    public function setServiceLevelWarning(?bool $serviceLevelWarning): DialogOutput
    {
        $this->serviceLevelWarning = $serviceLevelWarning;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getServiceLevelAverageAnswerSpeedWarning(): ?bool
    {
        return $this->serviceLevelAverageAnswerSpeedWarning;
    }

    /**
     * @param bool|null $serviceLevelAverageAnswerSpeedWarning
     * @return DialogOutput
     */
    public function setServiceLevelAverageAnswerSpeedWarning(?bool $serviceLevelAverageAnswerSpeedWarning): DialogOutput
    {
        $this->serviceLevelAverageAnswerSpeedWarning = $serviceLevelAverageAnswerSpeedWarning;
        return $this;
    }
}