<?php declare(strict_types=1);

namespace App\Object\User;

use App\Entity\Zone;

class UserOutput
{
    private ?string $id = null;
    private ?string $name = null;
    private ?int $dialogs = null;
    private ?int $callDialogs = null;
    private ?int $chatDialogs = null;
    private ?float $activeTime = null;
    private ?float $serviceLevel = null;
    private ?float $serviceLevelCall = null;
    private ?float $serviceLevelChat = null;
    private ?float $serviceLevelAverageSpeedAnswer = null;
    private ?float $serviceLevelAverageSpeedAnswerCall = null;
    private ?float $serviceLevelAverageSpeedAnswerChat = null;
    private ?float $knowledge = null;
    private ?float $scoring = null;
    private ?int $scoringCount = null;
    private ?int $criticalErrors = null;
    private ?float $scoringPoints = null;
    private ?float $scoringCall = null;
    private ?float $scoringChat = null;
    private ?float $scoreCoveringCall = null;
    private ?float $scoreCoveringChat = null;
    private ?float $totalScore = null;
    private ?Zone $zone = null;
    private ?bool $trainee = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
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

    public function getDialogs(): ?int
    {
        return $this->dialogs;
    }

    public function setDialogs(?int $dialogs): self
    {
        $this->dialogs = $dialogs;

        return $this;
    }

    public function getCallDialogs(): ?int
    {
        return $this->callDialogs;
    }

    public function setCallDialogs(?int $callDialogs): self
    {
        $this->callDialogs = $callDialogs;

        return $this;
    }

    public function getChatDialogs(): ?int
    {
        return $this->chatDialogs;
    }

    public function setChatDialogs(?int $chatDialogs): self
    {
        $this->chatDialogs = $chatDialogs;

        return $this;
    }

    public function getActiveTime(): ?float
    {
        return $this->activeTime;
    }

    public function setActiveTime(?float $activeTime): self
    {
        $this->activeTime = $activeTime;

        return $this;
    }

    public function getServiceLevelCall(): ?float
    {
        return $this->serviceLevelCall;
    }

    public function setServiceLevelCall(?float $serviceLevelCall): self
    {
        $this->serviceLevelCall = $serviceLevelCall;

        return $this;
    }

    public function getServiceLevelChat(): ?float
    {
        return $this->serviceLevelChat;
    }

    public function setServiceLevelChat(?float $serviceLevelChat): self
    {
        $this->serviceLevelChat = $serviceLevelChat;

        return $this;
    }

    public function getServiceLevel(): ?float
    {
        return $this->serviceLevel;
    }

    public function setServiceLevel(?float $serviceLevel): self
    {
        $this->serviceLevel = $serviceLevel;

        return $this;
    }

    public function getServiceLevelAverageSpeedAnswerChat(): ?float
    {
        return $this->serviceLevelAverageSpeedAnswerChat;
    }

    public function setServiceLevelAverageSpeedAnswerChat(?float $serviceLevelAverageSpeedAnswerChat): self
    {
        $this->serviceLevelAverageSpeedAnswerChat = $serviceLevelAverageSpeedAnswerChat;

        return $this;
    }

    public function getServiceLevelAverageSpeedAnswerCall(): ?float
    {
        return $this->serviceLevelAverageSpeedAnswerCall;
    }

    public function setServiceLevelAverageSpeedAnswerCall(?float $serviceLevelAverageSpeedAnswerCall): self
    {
        $this->serviceLevelAverageSpeedAnswerCall = $serviceLevelAverageSpeedAnswerCall;

        return $this;
    }

    public function getServiceLevelAverageSpeedAnswer(): ?float
    {
        return $this->serviceLevelAverageSpeedAnswer;
    }

    public function setServiceLevelAverageSpeedAnswer(?float $serviceLevelAverageSpeedAnswer): self
    {
        $this->serviceLevelAverageSpeedAnswer = $serviceLevelAverageSpeedAnswer;

        return $this;
    }

    public function getKnowledge(): ?float
    {
        return $this->knowledge;
    }

    public function setKnowledge(?float $knowledge): self
    {
        $this->knowledge = $knowledge;

        return $this;
    }

    public function getScoring(): ?float
    {
        return $this->scoring;
    }

    public function setScoring(?float $scoring): self
    {
        $this->scoring = $scoring;

        return $this;
    }

    public function getCriticalErrors(): ?int
    {
        return $this->criticalErrors;
    }

    public function setCriticalErrors(?int $criticalErrors): self
    {
        $this->criticalErrors = $criticalErrors;
        return $this;
    }

    public function getScoringCount(): ?int
    {
        return $this->scoringCount;
    }

    public function setScoringCount(?int $scoringCount): self
    {
        $this->scoringCount = $scoringCount;
        return $this;
    }

    public function getScoringPoints(): ?float
    {
        return $this->scoringPoints;
    }

    public function setScoringPoints(?float $scoringPoints): self
    {
        $this->scoringPoints = $scoringPoints;
        return $this;
    }

    public function getScoringCall(): ?float
    {
        return $this->scoringCall;
    }

    public function setScoringCall(?float $scoringCall): self
    {
        $this->scoringCall = $scoringCall;

        return $this;
    }

    public function getScoringChat(): ?float
    {
        return $this->scoringChat;
    }

    public function setScoringChat(?float $scoringChat): self
    {
        $this->scoringChat = $scoringChat;

        return $this;
    }

    public function getScoreCoveringCall(): ?float
    {
        return $this->scoreCoveringCall;
    }

    public function setScoreCoveringCall(?float $scoreCoveringCall): self
    {
        $this->scoreCoveringCall = $scoreCoveringCall;

        return $this;
    }

    public function getScoreCoveringChat(): ?float
    {
        return $this->scoreCoveringChat;
    }

    public function setScoreCoveringChat(?float $scoreCoveringChat): self
    {
        $this->scoreCoveringChat = $scoreCoveringChat;

        return $this;
    }

    public function getTotalScore(): ?float
    {
        return $this->totalScore;
    }

    public function setTotalScore(?float $totalScore): self
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function isTrainee(): ?bool
    {
        return $this->trainee;
    }

    public function setTrainee(?bool $trainee): self
    {
        $this->trainee = $trainee;
        return $this;
    }
}
