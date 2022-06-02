<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\DatesEntityTrait;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\Dialog\Type;
use App\Object\Call\CallInput;
use App\Object\Dialog\DialogOutput;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;

#[ApiResource(
    collectionOperations: [
        "get" => [
           "path" => "/admin/dialogs",
        ],
        "post" => [
            "path" => "/admin/dialogs",
        ],
        "importCallMessage" => [
            "method" => Request::METHOD_POST,
            "path" => "/dialog/import-call",
            "input" => CallInput::class,
            "input_formats" => [
                "multipart" => ['multipart/form-data'],
            ],
        ],
        "clientList" => [
            "method" => "get",
            "path" => "/dialogs",
            "output" => DialogOutput::class
        ],
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/dialogs/{id}",
        ],
        "put" => [
            "path" => "/admin/dialogs/{id}",
        ],
        "patch" => [
            "path" => "/admin/dialogs/{id}",
        ],
        "delete" => [
            "path" => "/admin/dialogs/{id}",
        ]
    ],
    attributes: [
        "order" => ["createdAt" => "DESC"],
        "pagination_items_per_page" => 25,
        "pagination_client_items_per_page" => true,
    ]
)]
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ["users" => "exact", "users.name" => "ipartial", "type" => "exact", "records.session" => "exact"])]
#[ApiFilter(DateFilter::class, properties: ["date"])]
#[ApiFilter(ExistsFilter::class, properties: ["scoring", "users"])]
class Dialog
{
    use IdentifiableEntityTrait, DatesEntityTrait;

    #[ORM\Column(type: "datetime")]
    private ?DateTime $date = null;

    /**
     * @var Collection<Scoring>
     */
    #[ORM\OneToMany(mappedBy: "dialog", targetEntity: Scoring::class, cascade: ["persist", "remove"])]
    private Collection $scoring;

    #[ORM\Column(type: "dialog.type")]
    #[Enum(Type::class)]
    private ?Type $type = null;

    /**
     * @var Collection<DialogRecord>
     */
    #[ORM\OneToMany(mappedBy: "dialog", targetEntity: DialogRecord::class, cascade: ["persist"])]
    private Collection $records;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "dialogs")]
    private Collection $users;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $firstAnswerSpeed = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $averageSpeedAnswer = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $serviceLevelWarning = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $serviceLevelAverageAnswerSpeedWarning = null;

    #[Pure] public function __construct()
    {
        $this->records = new ArrayCollection;
        $this->users = new ArrayCollection;
        $this->scoring = new ArrayCollection;
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
     * @return Dialog
     */
    public function setType(?Type $type): Dialog
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<DialogRecord>|DialogRecord[]
     */
    public function getRecords(): Collection|array
    {
        return $this->records;
    }

    public function addRecord(DialogRecord $dialogRecord): self
    {
        if (!$this->records->contains($dialogRecord)) {
            $this->records[] = $dialogRecord;
            $dialogRecord->setDialog($this);
        }

        return $this;
    }

    public function removeRecord(DialogRecord $dialogRecord): self
    {
        if ($this->records->contains($dialogRecord)) {
            $this->records->removeElement($dialogRecord);
            if ($dialogRecord->getDialog() === $this) {
                $dialogRecord->setDialog(null);
            }
        }

        return $this;
    }


    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

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

    /**
     * @return Collection<Scoring>|Scoring[]
     */
    public function getScoring(): Collection|array
    {
        return $this->scoring;
    }

    public function setScoring(Scoring $scoring): self
    {
        // set the owning side of the relation if necessary
        if ($scoring->getDialog() !== $this) {
            $scoring->setDialog($this);
        }

        return $this;
    }

    /**
     * @return Collection<User>|User[]
     */
    public function getUsers(): Collection|array
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addDialog($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeDialog($this);
        }

        return $this;
    }

    public function getFirstAnswerSpeed(): ?int
    {
        return $this->firstAnswerSpeed;
    }

    public function setFirstAnswerSpeed(?int $firstAnswerSpeed): Dialog
    {
        $this->firstAnswerSpeed = $firstAnswerSpeed;
        return $this;
    }

    public function getAverageSpeedAnswer(): ?int
    {
        return $this->averageSpeedAnswer;
    }

    public function setAverageSpeedAnswer(?int $averageSpeedAnswer): self
    {
        $this->averageSpeedAnswer = $averageSpeedAnswer;
        return $this;
    }

    public function isServiceLevelWarning(): ?bool
    {
        return $this->serviceLevelWarning;
    }

    public function setServiceLevelWarning(?bool $serviceLevelWarning): self
    {
        $this->serviceLevelWarning = $serviceLevelWarning;

        return $this;
    }

    public function isServiceLevelAverageAnswerSpeedWarning(): ?bool
    {
        return $this->serviceLevelAverageAnswerSpeedWarning;
    }

    public function setServiceLevelAverageAnswerSpeedWarning(?bool $serviceLevelAverageAnswerSpeedWarning): self
    {
        $this->serviceLevelAverageAnswerSpeedWarning = $serviceLevelAverageAnswerSpeedWarning;

        return $this;
    }

    public function getRecordsUrl(): array
    {
        return array_values(
            array_filter(
                array_unique($this->records->map(fn(DialogRecord $record) => $record->getSession())->getValues())
            )
        );
    }

}
