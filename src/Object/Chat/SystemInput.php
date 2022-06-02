<?php declare(strict_types=1);

namespace App\Object\Chat;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class SystemInput
{
    #[Assert\NotBlank]
    private ?string $clientId = null;
    private ?string $chatId = null;
    #[Assert\NotBlank]
    private ?int $created = null;
    #[Assert\NotBlank]
    private ?string $sessionId = null;

    private ?string $class = null;

    public function getClientId(): ?string
    {
        return $this->clientId;
    }
    #[SerializedName("chat_id")]
    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(?string $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(?int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

}
