<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use O2System\Psr\NotFoundExceptionInterface;
use Traversable;

/**
 * Class Environment
 *
 * @package O2System\Framework\Container
 */
class Environment implements
    \ArrayAccess,
    \IteratorAggregate,
    \Countable,
    \Serializable,
    \JsonSerializable,
    ContainerInterface
{
    /**
     * Environment::getIterator
     *
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($_ENV);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::exists
     *
     * Checks if the data exists on the storage.
     * An alias of Environment::__isset method.
     *
     * @param string $offset The object offset key.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function exists($offset)
    {
        return $this->__isset($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::__isset
     *
     * Implementing magic method __isset to simplify when checks if offset exists on PHP native session variable,
     * just simply calling isset( $globals[ 'offset' ] ).
     *
     * @param mixed $offset PHP native GLOBALS offset.
     *
     * @return bool
     */
    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::offsetExists
     *
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($_ENV[ $offset ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::::__get
     *
     * Implementing magic method __get to simplify gets PHP native session variable by requested offset,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param $offset
     *
     * @return mixed
     */
    public function &__get($offset)
    {
        return $_ENV[ $offset ];
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::__set
     *
     * Implementing magic method __set to simplify set PHP native GLOBALS variable,
     * just simply calling $globals->offset = 'foo'.
     *
     * @param mixed $offset PHP native GLOBALS offset.
     * @param mixed $value  PHP native GLOBALS offset value to set.
     */
    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::store
     *
     * Store the data into the storage.
     * An alias of Environment::__set method.
     *
     * @param string $offset The data offset key.
     * @param mixed  $value  The data to be stored.
     *
     * @return void
     */
    public function store($offset, $value)
    {
        $this->__set($offset, $value);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::offsetSet
     *
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $_ENV[ $offset ] = $value;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::remove
     *
     * Removes a data from the storage.
     * An alias of Environment::__unset method.
     *
     * @param string $offset The object offset key.
     *
     * @return void
     */
    public function remove($offset)
    {
        $this->__unset($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::__unset
     *
     * Implementing magic method __unset to simplify unset method, just simply calling
     * unset( $globals[ 'offset' ] ).
     *
     * @param mixed $offset PHP Native GLOBALS offset
     *
     * @return void
     */
    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::offsetUnset
     *
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($_ENV[ $offset ])) {
            unset($_ENV[ $offset ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::merge
     *
     * Merge new array of data into the data storage.
     *
     * @param array $data New array of data.
     *
     * @return array The old array of data storage.
     */
    public function merge(array $data)
    {
        $oldData = $_ENV;
        $_ENV = array_merge($_ENV, $data);

        return $oldData;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::exchange
     *
     * Exchange the array of data storage into the new array of data.
     *
     * @param array $data New array of data.
     *
     * @return array The old array of data storage.
     */
    public function exchange(array $data)
    {
        $oldData = $_ENV;
        $_ENV = $data;

        return $oldData;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::destroy
     *
     * Removes all object from the container and perform each object destruction.
     *
     * @return array Array of old storage items.
     */
    public function destroy()
    {
        $storage = $_ENV;

        $_ENV = [];

        return $storage;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::count
     *
     * Application of Countable::count method to count the numbers of contained objects.
     *
     * @see  http://php.net/manual/en/countable.count.php
     * @return int The numbers of data on the storage.
     */
    public function count()
    {
        return (int)count($_ENV);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::serialize
     *
     * Application of Serializable::serialize method to serialize the data storage.
     *
     * @see  http://php.net/manual/en/serializable.serialize.php
     *
     * @return string The string representation of the serialized data storage.
     */
    public function serialize()
    {
        return serialize($_ENV);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::unserialize
     *
     * Application of Serializable::unserialize method to unserialize and construct the data storage.
     *
     * @see  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the serialized data storage.
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $_ENV = unserialize($serialized);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::jsonSerialize
     *
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $_ENV;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::getArrayCopy
     *
     * Gets a copy of the data storage.
     *
     * @return array Returns a copy of the data storage.
     */
    public function getArrayCopy()
    {
        return $_ENV;
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::get
     *
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->offsetGet($id);
        }

        // @todo throw exception
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::has
     *
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return (bool)$this->offsetExists($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Environment::offsetGet
     *
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return (isset($_ENV[ $offset ])) ? $_ENV[ $offset ] : false;
    }
}