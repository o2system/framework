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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Cart
 *
 * @package O2System\Framework\Services
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
            'id'       => null,
            'sku'      => null,
            'quantity' => 1,
            'price'    => 0,
            'discount' => 0,
            'name'     => null,
            'options'  => [],
        ], $item);

        // set sku
        $sku = empty($item[ 'sku' ]) ? $item[ 'id' ] : $item[ 'sku' ];

        // set sub-total
        $item[ 'subTotal' ][ 'price' ] = $item[ 'price' ] * $item[ 'quantity' ];
        $item[ 'subTotal' ][ 'discount' ] = 0;

        if (is_numeric($item[ 'discount' ])) {
            $item[ 'subTotal' ][ 'discount' ] = $item[ 'subTotal' ][ 'price' ] - $item[ 'discount' ];
        } elseif (is_string($item[ 'discount' ]) && strpos($item[ 'discount' ], '+') !== false) {
            $discounts = explode('+', $item[ 'discount' ]);
            if (count($discounts)) {
                $item[ 'subTotal' ][ 'discount' ] = $item[ 'subTotal' ][ 'price' ] * (intval(reset($discounts)) / 100);
                foreach (array_slice($discounts, 1) as $discount) {
                    $item[ 'subTotal' ][ 'discount' ] += $item[ 'subTotal' ][ 'discount' ] * (intval($discount) / 100);
                }
            }
        } elseif (is_string($item[ 'discount' ]) && strpos($item[ 'discount' ], '%') !== false) {
            $item[ 'subTotal' ][ 'discount' ] = $item[ 'subTotal' ][ 'price' ] * (intval($item[ 'discount' ]) / 100);
        }

        $item[ 'subTotal' ][ 'weight' ] = $item[ 'weight' ] * $item[ 'quantity' ];

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
            $item[ 'subTotal' ][ 'price' ] = $item[ 'price' ] * $item[ 'quantity' ];
            $item[ 'subTotal' ][ 'weight' ] = $item[ 'weight' ] * $item[ 'quantity' ];

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

    // ------------------------------------------------------------------------

    /**
     * Card::destroy
     */
    public function destroy()
    {
        unset($_SESSION[ 'o2system' ][ 'cart' ]);
        parent::destroy();
    }
}