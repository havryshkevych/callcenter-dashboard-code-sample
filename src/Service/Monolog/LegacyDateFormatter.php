<?php declare(strict_types=1);

namespace App\Service\Monolog;

use DateTimeInterface;
use Monolog\Formatter\JsonFormatter;

class LegacyDateFormatter extends JsonFormatter
{
    protected function formatDate(DateTimeInterface $date): array
    {
        return (array)$date;
    }
}
