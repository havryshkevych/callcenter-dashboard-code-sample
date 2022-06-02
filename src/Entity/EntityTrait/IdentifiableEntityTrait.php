<?php declare(strict_types=1);

namespace App\Entity\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use App\ApiPlatform\UuidGenerator;

trait IdentifiableEntityTrait
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }
}
