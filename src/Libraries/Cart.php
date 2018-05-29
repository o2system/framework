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

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Cart
 *
 * @package O2System\Framework\Libraries
 */
class Cart extends AbstractRepository
{
    /**
     * Cart::__construct
     */
    public function __construct()
    {
        if (empty($_SESSION[ 'o2system' ][ 'cart' ])) {
            $_SESSION[ 'o2system' ][ 'cart' ] = [];
        }

        $this->storage =& $_SESSION[ 'o2system' ][ 'cart' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Cart::add
     *
     * @param array $item
     */
    public function add(array $item)
    {
        $item = array_merge([
            'id'      => null,
            'sku'     => null,
            'qty'     => 1,
            'price'   => 0,
            'name'    => null,
            'options' => [],
        ], $item);

        // set sku
        $sku = empty($item[ 'sku' ]) ? $item[ 'id' ] : $item[ 'sku' ];

        // set sub-total
        $item[ 'subTotal' ][ 'price' ] = $item[ 'price' ] * $item[ 'qty' ];
        $item[ 'subTotal' ][ 'weight' ] = $item[ 'weight' ] * $item[ 'qty' ];

        $this->storage[ $sku ] = $item;
    }

    // ------------------------------------------------------------------------

    /**
     * Cart::update
     *
     * @param string $sku
     * @param array  $item
     *
     * @return bool
     */
    public function update($sku, array $item)
    {
        if ($this->offsetExists($sku)) {
            $item = array_merge($this->offsetGet($sku), $item);

            // update sub-total
            $item[ 'subTotal' ][ 'price' ] = $item[ 'price' ] * $item[ 'qty' ];
            $item[ 'subTotal' ][ 'weight' ] = $item[ 'weight' ] * $item[ 'qty' ];

            $this->storage[ $sku ] = $item;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Cart::getTotalWeight
     *
     * @return int
     */
    public function getTotalWeight()
    {
        $totalWeight = 0;

        if ($this->count()) {
            foreach ($this->storage as $id => $item) {
                if (isset($item[ 'subTotal' ][ 'weight' ])) {
                    $totalWeight += (int)$item[ 'weight' ];
                }
            }
        }

        return $totalWeight;
    }

    // ------------------------------------------------------------------------

    /**
     * Cart::getTotalPrice
     *
     * @return int
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;

        if ($this->count()) {
            foreach ($this->storage as $id => $item) {
                if (isset($item[ 'subTotal' ][ 'price' ])) {
                    $totalPrice += (int)$item[ 'price' ];
                }
            }
        }

        return $totalPrice;
    }

    public function destroy()
    {
        unset($_SESSION[ 'o2system' ][ 'cart' ]);
        parent::destroy();

    }
}