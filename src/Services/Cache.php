<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Cache\Abstracts\AbstractItemPool;
use O2System\Cache\Adapters;
use O2System\Cache\Item;
use O2System\Psr\Cache\CacheItemInterface;
use O2System\Psr\Cache\CacheItemPoolInterface;
use O2System\Psr\SimpleCache\CacheInterface;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Cache
 * @package O2System\Framework\Services
 */
class Cache extends Adapters implements CacheItemPoolInterface, CacheInterface
{
    private $poolOffset = 'default';

    // ------------------------------------------------------------------------

    public function setItemPool($poolOffset)
    {
        if ($this->exists($poolOffset)) {
            $this->poolOffset = $poolOffset;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::getItems
     *
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        if (false !== ($cacheItem = $this->callPoolOffset('getItems', [$keys]))) {
            return $cacheItem;
        }

        return [];
    }

    // ------------------------------------------------------------------------

    public function callPoolOffset($method, array $args = [])
    {
        $poolOffset = $this->poolOffset;

        if ( ! $this->exists($poolOffset)) {
            $poolOffset = 'default';
        }

        $itemPool = $this->getItemPool($poolOffset);

        if ($itemPool instanceof AbstractItemPool) {
            if (method_exists($itemPool, $method)) {
                return call_user_func_array([&$itemPool, $method], $args);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::clear
     *
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return $this->callPoolOffset('clear');
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::saveDeferred
     *
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->callPoolOffset('saveDeferred', [$item]);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::commit
     *
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        return $this->callPoolOffset('commit');
    }

    // ------------------------------------------------------------------------

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        return (bool)$this->deleteItem($key);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::deleteItem
     *
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        return $this->callPoolOffset('deleteItem', [$key]);
    }

    // ------------------------------------------------------------------------

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as
     *                  value.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        $result = new ArrayIterator();

        foreach ($keys as $key) {
            if ($this->exists($key)) {
                $result[ $key ] = $this->get($key, $default);
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        if ($this->hasItem($key)) {
            $item = $this->getItem($key);

            return $item->get();
        }

        return $default;
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::hasItem
     *
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        return $this->callPoolOffset('hasItem', [$key]);
    }

    // ------------------------------------------------------------------------

    /**
     * Cache::getItem
     *
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        if (false !== ($cacheItem = $this->callPoolOffset('getItem', [$key]))) {
            return $cacheItem;
        }

        return new Item(null, null);
    }

    // ------------------------------------------------------------------------

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        $result = [];

        foreach ($values as $key => $value) {
            if ($this->set($key, $value, $ttl)) {
                $result[ $key ] = true;
            }
        }

        return (bool)count($result) == count($values);
    }

    // ------------------------------------------------------------------------

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->save(new Item($key, $value, $ttl));
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::save
     *
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        return $this->callPoolOffset('save', [$item]);
    }

    // ------------------------------------------------------------------------

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        return (bool)$this->deleteItems($keys);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::deleteItems
     *
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys = [])
    {
        return $this->callPoolOffset('deleteItems', [$keys]);
    }

    // ------------------------------------------------------------------------

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \O2System\Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        return $this->hasItem($key);
    }
}