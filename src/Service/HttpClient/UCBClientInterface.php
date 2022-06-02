<?php declare(strict_types=1);

namespace App\Service\HttpClient;

use App\Security\User;

interface UCBClientInterface
{
    public function getUser(string $token): ?User;
}
