<?php declare(strict_types=1);

namespace App\Service\Monolog;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiLoggerInterface
{
    public static function log(
        LoggerInterface $logger,
        Request $request,
        Response $response,
        ?float $duration = null,
        ?int $memory = null
    ): void;
}
