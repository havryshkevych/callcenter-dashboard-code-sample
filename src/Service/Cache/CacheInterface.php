<?php declare(strict_types=1);

namespace App\Service\Cache;

use App\Object\CacheResponse;
use Psr\Cache\CacheItemInterface;

interface CacheInterface
{
    public function getItem(string $key): CacheItemInterface;

    public function saveItem(CacheItemInterface $item, object $data, int $ttl, string $key = 'id'): void;

    /**
     * @param string[] $ids
     */
    public function getItems(string $prefix, array $ids): CacheResponse;

    /**
     * @param CacheItemInterface[] $missingItems
     * @param object[] $itemsData
     */
    public function saveMissingItems(iterable $missingItems, iterable $itemsData, int $ttl, string $key = 'id'): void;

    /**
     * @param CacheItemInterface[] $items
     * @return object[]
     */
    public function getItemsData(array $items): array;

    /**
     * @param string[] $keys
     */
    public function resetCache(array $keys): bool;

    /**
     * @param string[] $tags
     */
    public function invalidateTags(array $tags): bool;
}
