<?php

namespace App\ApiPlatform;

use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Doctrine\UuidGenerator as RamseyUuidGenerator;

class UuidGenerator extends RamseyUuidGenerator
{
    public function generate(EntityManager $em, $entity): string
    {
        return parent::generate($em, $entity)->toString();
    }
}