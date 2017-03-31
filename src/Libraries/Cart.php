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

namespace O2System\Framework\Libraries;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\AbstractItemStoragePattern;

/**
 * Class Cart
 *
 * @package O2System\Framework\Libraries
 */
class Cart extends AbstractItemStoragePattern
{
    /**
     * Cart::__construct
     */
    public function __construct()
    {
        if ( empty( $_SESSION[ 'o2system' ][ 'cart' ] ) ) {
            $_SESSION[ 'o2system' ][ 'cart' ] = [];
        }

        $this->storage =& $_SESSION[ 'o2system' ][ 'cart' ];
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractItemStoragePattern::__set
     *
     * Application of __set magic method to store the data into the storage.
     *
     * @param string $offset The data offset key.
     * @param mixed  $value  The data to be stored.
     *
     * @return void
     */
    public function __set( $offset, $value )
    {
        $this->storage[ $offset ] = $value;
    }

    // ------------------------------------------------------------------------

    public function getTotalWeight()
    {

    }

    public function getTotal()
    {

    }
}