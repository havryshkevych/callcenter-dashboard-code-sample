<?php declare(strict_types=1);

namespace App\Entity\EntityTrait;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait DatesEntityTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTime $updatedAt = null;

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function createDate(): void
    {
        $this->createdAt = new DateTime();
    }

    #[ORM\PrePersist, ORM\PreUpdate]
    public function updateDate(): void
    {
        $this->updatedAt = new DateTime();
    }
}
