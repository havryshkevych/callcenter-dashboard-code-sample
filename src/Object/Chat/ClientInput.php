<?php declare(strict_types=1);

namespace App\Object\Chat;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ClientInput
{
    #[Assert\NotBlank]
    private ?string $clientId = null;

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    #[SerializedName("chat_id")]
    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }
}
