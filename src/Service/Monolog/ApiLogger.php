<?php declare(strict_types=1);

namespace App\Service\Monolog;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLogger implements ApiLoggerInterface
{
    public static function log(
        LoggerInterface $logger,
        Request $request,
        Response $response,
        ?float $duration = null,
        ?int $memory = null
    ): void {
        $message = sprintf(
            '%s (%s) on %s %s',
            Response::$statusTexts[$response->getStatusCode()] ?? 'Unknown',
            $response->getStatusCode(),
            $request->getMethod(),
            $request->getRequestUri(),
        );
        $context = [
            'request' => [
                'method' => $request->getMethod(),
                'uri' => $request->getRequestUri(),
                'headers' => $request->headers->all(),
                'content' => $request->getContent(),
            ],
            'response' => [
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'content' => $response->getContent(),
            ],

        ];

        if (!is_null($duration)) {
            $context['stopwatch'] = [
                'time' => sprintf('%d ms', $duration),
                'raw-time' => $duration,
                'memory' => sprintf('%.2F MB', $memory / 1024 / 1024),
                'raw-memory' => $memory,
            ];
        }

        if ($response->isSuccessful()) {
            $logger->info($message, $context);
        } else {
            $logger->error($message, $context);
        }
    }
}
