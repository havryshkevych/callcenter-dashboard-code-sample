<?php declare(strict_types=1);

namespace App\Object\Call;

use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CallInput
{
    private ?string $clientId;
    private ?User $user;
    private ?DateTime $receivedAt;
    private ?int $duration;
    /**
     * @var Collection|File[]|UploadedFile[]
     */
    private Collection|array $records;

    #[Pure]
    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;
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

    public function getReceivedAt(): ?DateTime
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(?DateTime $receivedAt): self
    {
        $this->receivedAt = $receivedAt;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getRecords(): ?array
    {
        return $this->records;
    }

    public function setRecords(?array $records): self
    {
        $this->records = $records;
        return $this;
    }
}
