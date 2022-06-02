<?php declare(strict_types=1);

namespace App\Object;

class CacheResponse
{
    protected array $items = [];
    protected array $missingItems = [];

    public static function create(array $items, array $missingItems): self
    {
        return (new static())->setItems($items)->setMissingItems($missingItems);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getMissingItems(): array
    {
        return $this->missingItems;
    }

    public function setMissingItems(array $missingItems): self
    {
        $this->missingItems = $missingItems;

        return $this;
    }
}
