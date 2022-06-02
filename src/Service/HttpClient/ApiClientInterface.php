<?php declare(strict_types=1);

namespace App\Service\HttpClient;

use Psr\Log\LoggerInterface;

interface ApiClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        array $query = [],
        array $content = []
    ): string;

    public function setLogger(LoggerInterface $logger): self;
}
