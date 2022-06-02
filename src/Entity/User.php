<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use App\Enum\User\Role;
use App\Object\User\SupervisorOutput;
use App\Object\User\UserOutput;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/users"
        ],
        "post" => [
            "path" => "/admin/users"
        ],
        "scoring" => [
            "method" => Request::METHOD_GET,
            "path" => "/users/scoring",
            "output" => UserOutput::class,
            "output_formats" => ["json" => "application/json", "jsonld" => "application/ld+json"],
            "pagination_enabled" => false,
            "openapi_context" => [
                "parameters" => [
                    ["name" => "dialogs.date[before]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[strictly_before]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[after]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[strictly_after]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                ]
            ]
        ],
        "scoringSupervisor" => [
            "method" => Request::METHOD_GET,
            "path" => "/users/scoring-supervisors",
            "output" => SupervisorOutput::class,
            "output_formats" => ["json" => "application/json", "jsonld" => "application/ld+json"],
            "pagination_enabled" => false,
            "openapi_context" => [
                "parameters" => [
                    ["name" => "dialogs.date[before]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[strictly_before]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[after]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                    ["name" => "dialogs.date[strictly_after]", "in" => "path", "schema" => ["type" => "string"], "required" => false, "description" => "scoring data date filter"],
                ]
            ]
        ]
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/users/{id}",
        ],
        "put" => [
            "path" => "/admin/users/{id}",
        ],
        "patch" => [
            "path" => "/admin/users/{id}",
        ],
        "delete" => [
            "path" => "/admin/users/{id}",
        ],
        "profile" => [
            "method" => Request::METHOD_GET,
            "path" => "/users/{id}",
            //todo: add groups and remove heavy fields activeTimes, knowledgeScorings, dialogs,
            // (?ranks - hide Ranks under GroupFilter) || or add property fields filter
        ],
    ],
    attributes: [
        "pagination_items_per_page" => 25,
        "pagination_client_items_per_page" => true,
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(["chatId", "callId"])]
#[ApiFilter(SearchFilter::class, properties: [
    "id" => "exact",
    "dialogs" => "exact",
    "roles" => "partial",
    "name" => "ipartial",
    "email" => "ipartial",
    "callId" => "ipartial",
    "chatId" => "ipartial",
])]
class User implements UserInterface
{
    use IdentifiableEntityTrait;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: true)]
    private ?string $callId = null;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: true)]
    private ?string $chatId = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name = '';

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: ActiveTime::class, cascade: ["remove"], fetch: "EXTRA_LAZY")]
    private Collection $activeTimes;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: KnowledgeScoring::class, cascade: ["remove"], fetch: "EXTRA_LAZY")]
    private Collection $knowledgeScorings;

    #[ORM\ManyToMany(targetEntity: Dialog::class, inversedBy: "users", fetch: "EXTRA_LAZY")]
    #[ORM\JoinTable(name: "users_dialogs")]
    private Collection $dialogs;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: UserRank::class, cascade: ["persist", "remove"], fetch: "EXTRA_LAZY")]
    private Collection $ranks;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $workStartAt = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => true])]
    private bool $active = false;

    #[ORM\OneToMany(mappedBy: "supervisor", targetEntity: User::class)]
    private Collection $operators;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "operators")]
    #[ORM\JoinColumn(name: "supervisor", referencedColumnName: "id")]
    private ?User $supervisor = null;

    #[Pure] public function __construct()
    {
        $this->activeTimes = new ArrayCollection();
        $this->knowledgeScorings = new ArrayCollection();
        $this->dialogs = new ArrayCollection();
        $this->ranks = new ArrayCollection();
        $this->operators = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCallId(): ?string
    {
        return $this->callId;
    }

    public function setCallId(?string $callId): self
    {
        $this->callId = $callId;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<ActiveTime>|ActiveTime[]
     */
    public function getActiveTimes(): Collection|array
    {
        return $this->activeTimes;
    }

    public function addActiveTime(ActiveTime $activeTime): self
    {
        if (!$this->activeTimes->contains($activeTime)) {
            $this->activeTimes[] = $activeTime;
            $activeTime->setUser($this);
        }

        return $this;
    }

    public function removeActiveTime(ActiveTime $activeTime): self
    {
        if ($this->activeTimes->contains($activeTime)) {
            $this->activeTimes->removeElement($activeTime);
            if ($activeTime->getUser() === $this) {
                $activeTime->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<KnowledgeScoring>|KnowledgeScoring[]
     */
    public function getKnowledgeScorings(): Collection|array
    {
        return $this->knowledgeScorings;
    }

    public function addKnowledgeScoring(KnowledgeScoring $knowledgeScoring): self
    {
        if (!$this->knowledgeScorings->contains($knowledgeScoring)) {
            $this->knowledgeScorings[] = $knowledgeScoring;
            $knowledgeScoring->setUser($this);
        }

        return $this;
    }

    public function removeKnowledgeScoring(KnowledgeScoring $knowledgeScoring): self
    {
        if ($this->knowledgeScorings->contains($knowledgeScoring)) {
            $this->knowledgeScorings->removeElement($knowledgeScoring);
            if ($knowledgeScoring->getUser() === $this) {
                $knowledgeScoring->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Dialog>|Dialog[]
     */
    public function getDialogs(): Collection|array
    {
        return $this->dialogs;
    }

    public function addDialog(Dialog $dialog): self
    {
        if (!$this->dialogs->contains($dialog)) {
            $this->dialogs[] = $dialog;
            $dialog->addUser($this);
        }

        return $this;
    }

    public function removeDialog(Dialog $dialog): self
    {
        if ($this->dialogs->contains($dialog)) {
            $this->dialogs->removeElement($dialog);
            $dialog->removeUser($this);
        }

        return $this;
    }

    public function getWorkStartAt(): ?DateTime
    {
        return $this->workStartAt;
    }

    public function setWorkStartAt(?DateTime $workStartAt): self
    {
        $this->workStartAt = $workStartAt;
        return $this;
    }

    public function getCurrentRank(?Role $role = null, ?array $dates = null): ?UserRank
    {
        $role = $role ?: Role::OPERATOR();
        if (!$dates) {
            $dates = [
                "after" => (new DateTime())->modify('midnight first day of this month')->format("Y-m-d H:i:s"),
                "before" => (new DateTime())->modify('midnight first day of next month')->format("Y-m-d H:i:s")
            ];
        }
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gt("date", new DateTime($dates['after'])))
            ->andWhere(Criteria::expr()->lt("date", new DateTime($dates['before'])));

        $currentRank = $this->ranks->matching($criteria->andWhere(Criteria::expr()->eq('type', $role)))->last();

        if (!$currentRank instanceof UserRank) {
            return null;
        }

        return $currentRank;
    }

    /**
     * @return Collection<UserRank>|UserRank[]
     */
    public function getRanks(): Collection|array
    {
        return $this->ranks;
    }

    public function addRank(UserRank $rank): self
    {
        if (!$this->ranks->contains($rank)) {
            $this->ranks[] = $rank;
            $rank->setUser($this);
        }

        return $this;
    }

    public function removeRank(UserRank $rank): self
    {
        if ($this->ranks->contains($rank)) {
            $this->ranks->removeElement($rank);
            if ($rank->getUser() === $this) {
                $rank->setUser(null);
            }
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return Collection<User>|User[]
     */
    public function getOperators(): Collection|array
    {
        return $this->operators;
    }

    public function addOperator(User $operator): self
    {
        if (!$this->operators->contains($operator)) {
            $this->operators[] = $operator;
            $operator->setSupervisor($this);
        }

        return $this;
    }

    public function removeOperator(User $operator): self
    {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
            if ($operator->getSupervisor() === $this) {
                $operator->setSupervisor(null);
            }
        }

        return $this;
    }

    public function getSupervisor(): ?User
    {
        return $this->supervisor;
    }

    public function setSupervisor(?User $supervisor): self
    {
        $this->supervisor = $supervisor;
        return $this;
    }
}
