<?php declare(strict_types=1);

namespace App\Service\Monolog;

class SourceProcessor
{
    private string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function __invoke(array $record): array
    {
        if (empty($this->source)) {
            return $record;
        }

        $record['extra']['source'] = $this->source;

        return $record;
    }
}
