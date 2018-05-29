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
/**
 * Number Helper
 *
 * A collection of helper function to work with a numeric value.
 */
// ------------------------------------------------------------------------

if ( ! function_exists('is_positive')) {
    /**
     * is_positive
     *
     * Determine if the number is a positive value.
     *
     * @param int $number The numeric value.
     *
     * @return  bool
     */
    function is_positive($number)
    {
        if ($number > 0) {
            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('is_negative')) {
    /**
     * is_negative
     *
     * Determine if the number is a negative value.
     *
     * @param int $number The numeric value.
     *
     * @return  bool
     */
    function is_negative($number)
    {
        if ($number < 0) {
            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('is_odd')) {
    /**
     * is_odd
     *
     * Determine if the number is an odd value.
     *
     * @param int $number The numeric value.
     *
     * @return  bool
     */
    function is_odd($number)
    {
        if ($number % 2 == 0) {
            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('is_even')) {
    /**
     * is_even
     *
     * Determine if the number is an even value.
     *
     * @param int $number The numeric value.
     *
     * @return  bool
     */
    function is_even($number)
    {
        if ($number % 2 == 0) {
            return false;
        }

        return true;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('currency_format')) {
    /**
     * currency_format
     *
     * Format a number into string of formatted currency value.
     *
     * @param int    $number    The numeric value.
     * @param string $locale    The locale code indicating the language to use.
     * @param string $currency  The 3-letter ISO 4217 currency code indicating the currency to use.
     * @param bool   $add_space Add a space between currency and the formatted number.
     *
     * @return string
     */
    function currency_format($number, $locale = 'en_US', $currency = 'USD', $add_space = false)
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        if ($add_space) {
            $formatter->setPattern(str_replace('¤#', '¤ #', $formatter->getPattern()));
        }

        return $formatter->formatCurrency($number, $currency);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('unit_format')) {
    /**
     * unit format
     *
     * Format a number with grouped thousands and added a custom unit suffix.
     *
     * @param int    $number              The numeric value.
     * @param string $unit                The custom unit suffix
     * @param int    $decimals            The number of decimal points.
     * @param string $decimal_point       The separator for the decimal point.
     * @param string $thousands_separator The thousands separator.
     *
     * @return string
     */
    function unit_format($number, $unit = null, $decimals = 0, $decimal_point = '.', $thousands_separator = ',')
    {
        $number = number_format($number, $decimals, $decimal_point, $thousands_separator);

        if (isset($unit)) {
            return $number . ' ' . $unit;
        }

        return $number;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('hertz_format')) {
    /**
     * hertz_format
     *
     * Formats a numbers into a string with the appropriate hertz unit based on size.
     *
     * @param int $number   The numeric value.
     * @param int $decimals The number of decimal points.
     *
     * @return  string
     */
    function hertz_format($number, $decimals = 1)
    {
        if ($number >= 1000000000000) {
            $number = round($number / 1099511627776, $decimals);
            $unit = 'THz';
        } elseif ($number >= 1000000000) {
            $number = round($number / 1073741824, $decimals);
            $unit = 'GHz';
        } elseif ($number >= 1000000) {
            $number = round($number / 1048576, $decimals);
            $unit = 'MHz';
        } elseif ($number >= 1000) {
            $number = round($number / 1024, $decimals);
            $unit = 'KHz';
        } else {
            $unit = 'Hz';

            return number_format($number) . ' ' . $unit;
        }

        return number_format($number, $decimals) . ' ' . $unit;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('roman_format')) {
    /**
     * roman_format
     *
     * Formats a number into string of roman format.
     *
     * @param int $number The numeric value.
     *
     * @return  string
     */
    function roman_format($number)
    {
        $romans = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $return = '';

        while ($number > 0) {
            foreach ($romans as $rom => $arb) {
                if ($number >= $arb) {
                    $number -= $arb;
                    $return .= $rom;
                    break;
                }
            }
        }

        return $return;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('short_format')) {
    /**
     * short_format
     *
     * Formats a number into shorted string with the appropriate unit based on size.
     *
     * @param int $number   The numeric value.
     * @param int $decimals The number of decimal points.
     *
     * @return string
     */
    function short_format($number, $decimals = 0)
    {
        $divisors = [
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        ];

        // Loop through each $divisor and find the
        // lowest amount that matches
        foreach ($divisors as $divisor => $shorthand) {
            if ($number < ($divisor * 1000)) {
                // We found a match!
                // We found our match, or there were no matches.
                // Either way, use the last defined value for $divisor.
                return number_format($number / $divisor, $decimals) . $shorthand;
                break;
            }
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('byte_format')) {
    /**
     * byte_format
     *
     * Formats a numbers into a string with the appropriate byte unit based on size.
     *
     * @param int $number   The numeric value.
     * @param int $decimals The number of decimal points.
     *
     * @return    string
     */
    function byte_format($number, $decimals = 1)
    {
        language()->loadFile('number');

        if ($number >= 1000000000000) {
            $number = round($number / 1099511627776, $decimals);
            $unit = language()->getLine('TERABYTE_ABBR');
        } elseif ($number >= 1000000000) {
            $number = round($number / 1073741824, $decimals);
            $unit = language()->getLine('GIGABYTE_ABBR');
        } elseif ($number >= 1000000) {
            $number = round($number / 1048576, $decimals);
            $unit = language()->getLine('MEGABYTE_ABBR');
        } elseif ($number >= 1000) {
            $number = round($number / 1024, $decimals);
            $unit = language()->getLine('KILOBYTE_ABBR');
        } else {
            $unit = language()->getLine('BYTES');

            return number_format($number) . ' ' . $unit;
        }

        return number_format($number, $decimals) . ' ' . $unit;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('ordinal_format')) {
    /**
     * ordinal_format
     *
     * Formats a number into an ordinal string such as 1st, 2nd, 3rd, 4th.
     *
     * @param int $number The numeric value.
     *
     * @return    string
     */
    function ordinal_format($number)
    {
        $suffixes =
            [
                'th',
                'st',
                'nd',
                'rd',
                'th',
                'th',
                'th',
                'th',
                'th',
                'th',
            ];

        return $number . ($number % 100 >= 11 && $number % 100 <= 13
                ? 'th'
                : $suffixes[ $number % 10 ]);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('words_format')) {
    /**
     * words_format
     *
     * Formats a number into a words of spelling string.
     *
     * @param int    $number The numeric value.
     * @param string $locale The locale code indicating the language to use.
     *
     * @return string
     */
    function words_format($number, $locale = 'en_US')
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);

        return $formatter->format($number);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('zero_fill')) {
    /**
     * zero_fill
     *
     * Formats a number into a string of leading zero number.
     *
     * @param int $number The numeric value
     * @param int $digits The number of digits.
     *
     * @return string
     */
    function zero_fill($number, $digits = 2)
    {
        return str_pad($number, $digits, '0', STR_PAD_LEFT);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('calculate')) {
    /**
     * Calculate
     *
     * Calculate from string
     *
     * @param   string $formula
     *
     * @return  string
     */
    function calculate($formula)
    {
        static $function_map = [
            'floor'   => 'floor',
            'ceil'    => 'ceil',
            'round'   => 'round',
            'sin'     => 'sin',
            'cos'     => 'cos',
            'tan'     => 'tan',
            'asin'    => 'asin',
            'acos'    => 'acos',
            'atan'    => 'atan',
            'abs'     => 'abs',
            'log'     => 'log',
            'pi'      => 'pi',
            'exp'     => 'exp',
            'min'     => 'min',
            'max'     => 'max',
            'rand'    => 'rand',
            'fmod'    => 'fmod',
            'sqrt'    => 'sqrt',
            'deg2rad' => 'deg2rad',
            'rad2deg' => 'rad2deg',
        ];

        // Remove any whitespace
        $formula = strtolower(preg_replace('~\s+~', '', $formula));

        // Empty formula
        if ($formula === '') {
            trigger_error('Empty formula', E_USER_ERROR);

            return null;
        }

        // Illegal function
        $formula = preg_replace_callback(
            '~\b[a-z]\w*\b~',
            function ($match) use ($function_map) {
                $function = $match[ 0 ];
                if ( ! isset($function_map[ $function ])) {
                    trigger_error("Illegal function '{$match[0]}'", E_USER_ERROR);

                    return '';
                }

                return $function_map[ $function ];
            },
            $formula
        );

        // Invalid function calls
        if (preg_match('~[a-z]\w*(?![\(\w])~', $formula, $match) > 0) {
            trigger_error("Invalid function call '{$match[0]}'", E_USER_ERROR);

            return null;
        }

        // Legal characters
        if (preg_match('~[^-+/%*&|<>!=.()0-9a-z,]~', $formula, $match) > 0) {
            trigger_error("Illegal character '{$match[0]}'", E_USER_ERROR);

            return null;
        }

        return eval("return({$formula});");
    }
}
