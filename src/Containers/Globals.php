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

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use Traversable;

/**
 * Class Globals
 *
 * @package O2System\Framework\Container
 */
class Globals implements \ArrayAccess, \IteratorAggregate
{
    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator( $GLOBALS );
    }

    // ------------------------------------------------------------------------

    /**
     * Globals::__isset
     *
     * Implementing magic method __isset to simplify when checks if offset exists on PHP native session variable,
     * just simply calling isset( $globals[ 'offset' ] ).
     *
     * @param mixed $offset PHP native GLOBALS offset.
     *
     * @return bool
     */
    public function __isset( $offset )
    {
        return $this->offsetExists( $offset );
    }

    // ------------------------------------------------------------------------

    /**
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
    public function offsetExists( $offset )
    {
        return isset( $GLOBALS[ $offset ] );
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__get
     *
     * Implementing magic method __get to simplify gets PHP native session variable by requested offset,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param $offset
     *
     * @return mixed
     */
    public function &__get( $offset )
    {
        return ( isset( $GLOBALS[ $offset ] ) ) ? $GLOBALS[ $offset ] : $GLOBALS[ $offset ] = null;
    }

    // ------------------------------------------------------------------------

    /**
     * Globals::__set
     *
     * Implementing magic method __set to simplify set PHP native GLOBALS variable,
     * just simply calling $globals->offset = 'foo'.
     *
     * @param mixed $offset PHP native GLOBALS offset.
     * @param mixed $value  PHP native GLOBALS offset value to set.
     */
    public function __set( $offset, $value )
    {
        $this->offsetSet( $offset, $value );
    }

    // ------------------------------------------------------------------------

    /**
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
    public function offsetSet( $offset, $value )
    {
        $GLOBALS[ $offset ] = $value;
    }

    // ------------------------------------------------------------------------

    /**
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
    public function offsetGet( $offset )
    {
        return ( isset( $GLOBALS[ $offset ] ) ) ? $GLOBALS[ $offset ] : false;
    }

    // ------------------------------------------------------------------------

    /**
     * Globals::__unset
     *
     * Implementing magic method __unset to simplify unset method, just simply calling
     * unset( $globals[ 'offset' ] ).
     *
     * @param mixed $offset PHP Native GLOBALS offset
     *
     * @return void
     */
    public function __unset( $offset )
    {
        $this->offsetUnset( $offset );
    }

    // ------------------------------------------------------------------------

    /**
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
    public function offsetUnset( $offset )
    {
        if ( isset( $GLOBALS[ $offset ] ) ) {
            unset( $GLOBALS[ $offset ] );
        }
    }
}