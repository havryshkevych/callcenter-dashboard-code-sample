<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\EntityTrait\DatesEntityTrait;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\DialogRecord\Sender;
use App\Object\Chat\ChatOutput;
use App\Object\Chat\ClientInput;
use App\Object\Chat\OperatorInput;
use App\Object\Chat\SystemInput;
use App\Repository\DialogRecordRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Symfony\Component\HttpFoundation\Request;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/dialog-records",
        ],
        "post" => [
            "path" => "/admin/dialog-records",
        ],
        "importChatMessage" => [
            "method" => Request::METHOD_POST,
            "path" => "/dialog-records/import-client-message",
            "input" => ClientInput::class,
            "output" => ChatOutput::class
        ],
        "importChatOperatorMessage" => [
            "method" => Request::METHOD_POST,
            "path" => "/dialog-records/import-operator-message",
            "input" => OperatorInput::class
        ],
        "importChatSystemMessage" => [
            "method" => Request::METHOD_POST,
            "path" => "/dialog-records/import-system-message",
            "input" => SystemInput::class
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/dialog-records/{id}",
        ],
        "patch" => [
            "path" => "/admin/dialog-records/{id}",
        ],
        "put" => [
            "path" => "/admin/dialog-records/{id}",
        ],
        "delete" => [
            "path" => "/admin/dialog-records/{id}",
        ]
    ],
    attributes: ["order" => ["createdAt" => "DESC"]]
)]
#[ORM\Entity(repositoryClass: DialogRecordRepository::class), ORM\HasLifecycleCallbacks]
class DialogRecord
{
    use IdentifiableEntityTrait, DatesEntityTrait;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTime $receivedAt = null;

    #[ORM\ManyToOne(targetEntity: Dialog::class, cascade: ["persist"], fetch: "EXTRA_LAZY", inversedBy: "records")]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Dialog $dialog = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $chatId = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $clientId = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $session = null;

    #[ORM\Column(type: "dialogRecord.sender", nullable: true)]
    #[Enum(Sender::class)]
    private ?Sender $sender = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $senderId = null;

    public function getDialog(): ?Dialog
    {
        return $this->dialog;
    }

    public function setDialog(?Dialog $dialog): DialogRecord
    {
        $this->dialog = $dialog;

        return $this;
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

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): self
    {
        $this->session = $session;

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

    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    public function setSender(?Sender $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function setSenderId(?string $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }
}
