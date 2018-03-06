<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 14/02/18
 * Time: 09.08
 */

namespace O2System\Framework\Datastructures\Commons;


use O2System\Psr\Patterns\AbstractItemStoragePattern;

class Money extends AbstractItemStoragePattern
{
    public function __construct($amount)
    {
        if (is_numeric($amount)) {
            $money[ 'amount' ] = (int)$amount;
        } elseif (is_array($amount)) {
            $money = $amount;
        }

        (int)$storage[ 'amount' ] = 0;
        $storage[ 'currency' ] = config()->getItem('currency');

        $storage = array_merge($storage, $money);
        (int)$storage[ 'amount' ] = empty($storage[ 'amount' ]) ? 0 : abs($storage[ 'amount' ]);

        $this->storage = $storage;
    }

    public function shortFormat($decimals = 0)
    {
        return short_format($this->amount, $decimals);
    }

    public function currencyFormat($locale = 'id_ID', $currency = 'IDR', $decimal = 0, $addSpace = true)
    {
        return currency_format($this->amount, 'id_ID', 'IDR', $decimal, $addSpace);
    }

    public function numberFormat($decimals = 0, $thousandSeparator = '.', $decimalSeparator = ',')
    {
        $decimalSeparator = $thousandSeparator === '.' ? ',' : '.';

        return number_format($this->amount, $decimals, $decimalSeparator, $thousandSeparator);
    }

    public function isSufficient($amount)
    {
        if ($amount < $this->amount) {
            return true;
        }

        return false;
    }

    public function __toString()
    {
        loader()->loadHelper('number');

        return $this->currencyFormat();
    }
}