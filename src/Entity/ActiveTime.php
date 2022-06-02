<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\EntityTrait\DatesEntityTrait;
use App\Entity\EntityTrait\IdentifiableEntityTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;


#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/admin/active-times",
        ],
        "post" => [
            "path" => "/admin/active-times",
        ],
        "clientList" => [
            "method" => Request::METHOD_GET,
            "path" => "/active-time"
        ]
    ],
    itemOperations: [
        "get" => [
            "path" => "/admin/active-times/{id}",
        ],
        "put" => [
            "path" => "/admin/active-times/{id}",
        ],
        "patch" => [
            "path" => "/admin/active-times/{id}",
        ],
        "delete" => [
            "path" => "/admin/active-times/{id}",
        ],
    ],
)]
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ["user" => "exact"])]
#[ApiFilter(DateFilter::class, properties: ["date"])]
class ActiveTime
{
    use IdentifiableEntityTrait, DatesEntityTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "activeTimes")]
    private ?User $user = null;

    #[ORM\Column(type: 'integer')]
    private ?int $seconds = 0;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $date = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSeconds(): ?int
    {
        return $this->seconds;
    }

    public function setSeconds(?int $seconds): self
    {
        $this->seconds = $seconds;
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
}
