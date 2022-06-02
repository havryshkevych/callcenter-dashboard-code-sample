<?php declare(strict_types=1);

namespace App\Event\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchListener
{
    const STOPWATCH = 'request-response';

    public function __construct(protected Stopwatch $stopwatch)
    {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $this->stopwatch->start(self::STOPWATCH);
    }
}
