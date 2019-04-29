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

namespace O2System\Framework\DataStructures\Commons;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Money
 * @package O2System\Framework\DataStructures\Commons
 */
class Money extends AbstractRepository
{
    /**
     * Money::__construct
     *
     * @param int $amount
     */
    public function __construct($amount)
    {
        $money = [];
        if (is_numeric($amount)) {
            $money[ 'amount' ] = (int)$amount;
        } elseif (is_array($amount)) {
            $money = $amount;
        }

        (int)$storage[ 'amount' ] = 0;
        $storage[ 'currency' ] = config()->getItem('units')->currency;

        $storage = array_merge($storage, $money);
        (int)$storage[ 'amount' ] = empty($storage[ 'amount' ]) ? 0 : abs($storage[ 'amount' ]);

        $this->storage = $storage;
    }

    // ------------------------------------------------------------------------

    /**
     * Money::shortFormat
     *
     * @param int $decimals
     *
     * @return string
     */
    public function shortFormat($decimals = 0)
    {
        return short_format($this->amount, $decimals);
    }

    // ------------------------------------------------------------------------

    /**
     * Money::numberFormat
     *
     * @param int    $decimals
     * @param string $thousandSeparator
     * @param string $decimalSeparator
     *
     * @return string
     */
    public function numberFormat($decimals = 0, $thousandSeparator = '.', $decimalSeparator = ',')
    {
        $decimalSeparator = $thousandSeparator === '.' ? ',' : '.';

        return number_format($this->amount, $decimals, $decimalSeparator, $thousandSeparator);
    }

    // ------------------------------------------------------------------------

    /**
     * Money::isSufficient
     *
     * @param int $amount
     *
     * @return bool
     */
    public function isSufficient($amount)
    {
        if ($amount < $this->amount) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Money::__toString
     *
     * @return string
     */
    public function __toString()
    {
        loader()->loadHelper('number');

        return $this->currencyFormat();
    }

    // ------------------------------------------------------------------------

    /**
     * Money::currencyFormat
     *
     * @param string $locale
     * @param string $currency
     * @param bool   $addSpace
     *
     * @return string
     */
    public function currencyFormat($locale = 'id_ID', $currency = 'IDR', $addSpace = true)
    {
        return currency_format($this->amount, $locale, $currency, $addSpace);
    }
}