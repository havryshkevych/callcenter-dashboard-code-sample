<?php declare(strict_types=1);

namespace App\Service\Cache;

use App\Object\CacheResponse;
use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException as CacheInvalidArgumentException;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class Cache implements CacheInterface
{
    public function __construct(protected TagAwareCacheInterface $cache)
    {
    }

    public function getItem(string $key): CacheItemInterface
    {
        return $this->cache->getItem($key);
    }

    public function saveItem(CacheItemInterface $item, mixed $data, int $ttl, string $key = 'id'): void
    {
        $getter = 'get'.ucfirst($key);
        if (!method_exists($data, $getter)) {
            throw new InvalidArgumentException(
                sprintf('The %s to be cached must have a method %s', $data::class, $getter)
            );
        }

        $item->set($data);
        $item->tag($data->{$getter}());
        $item->expiresAfter($ttl);
    }

    /**
     * @param string $prefix
     * @param string[] $ids
     *
     * @return CacheResponse
     */
    public function getItems(string $prefix, array $ids): CacheResponse
    {
        $items = $missingItems = [];
        $keys = self::getKeys($prefix, $ids);
        /** @var CacheItemInterface $item */
        foreach ($this->cache->getItems($keys) as $item) {
            $id = self::getId($prefix, $item->getKey());
            $items[$id] = $item;
            if (!$item->isHit()) {
                $missingItems[$id] = $item;
            }
        }

        return CacheResponse::create($items, $missingItems);
    }

    /**
     * @param string $prefix
     * @param string[] $ids
     *
     * @return string[]
     */
    protected static function getKeys(string $prefix, array $ids): array
    {
        return array_map(fn(string $id) => preg_replace('/\W/', '-', $prefix.$id), $ids);
    }

    protected static function getId(string $prefix, string $key): string
    {
        if (str_starts_with($key, $prefix)) {
            return substr($key, strlen($prefix));
        }

        return $key;
    }

    /**
     * @param CacheItemInterface[] $missingItems
     * @param iterable $itemsData
     * @param int $ttl
     * @param string $key
     */
    public function saveMissingItems(iterable $missingItems, iterable $itemsData, int $ttl, string $key = 'id'): void
    {
        $getter = 'get'.ucfirst($key);
        foreach ($itemsData as $itemData) {
            if (!method_exists($itemData, $getter)) {
                throw new InvalidArgumentException(
                    sprintf('The %s to be cached must have a method %s', $itemData::class, $getter)
                );
            }
            $itemKey = $itemData->{$getter}();
            if (!array_key_exists($itemKey, $missingItems)) {
                continue;
            }
            $item = $missingItems[$itemKey];
            $item->set($itemData);
            $item->tag($itemKey);
            $item->expiresAfter($ttl);
            $this->cache->saveDeferred($item);
        }

        if (!empty($items)) {
            $this->cache->commit();
        }
    }

    /**
     * @param CacheItemInterface[] $items
     *
     * @return array
     */
    public function getItemsData(array $items): array
    {
        $itemsData = [];
        array_walk(
            $items,
            function (CacheItemInterface $item, string $key) use (&$itemsData) {
                if ($itemData = $item->get()) {
                    $itemsData[$key] = $itemData;
                }
            }
        );

        return $itemsData;
    }

    /**
     * @param string[] $keys
     */
    public function resetCache(array $keys): bool
    {
        return $this->cache->deleteItems($keys);
    }

    /**
     * @param string[] $tags
     *
     * @throws CacheInvalidArgumentException
     */
    public function invalidateTags(array $tags): bool
    {
        return $this->cache->invalidateTags($tags);
    }
}
