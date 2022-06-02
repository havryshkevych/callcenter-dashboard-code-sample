<?php declare(strict_types=1);

namespace App\Event\Listener;

use App\Service\Monolog\ApiLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ApiListener
{
    public function __construct(protected LoggerInterface $logger, protected Stopwatch $stopwatch)
    {
    }

    public function onKernelTerminate(TerminateEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $stopwatchEvent = $this->stopwatch->stop(StopwatchListener::STOPWATCH);
        ApiLogger::log(
            $this->logger,
            $event->getRequest(),
            $event->getResponse(),
            $stopwatchEvent->getDuration(),
            $stopwatchEvent->getMemory()
        );
    }
}
