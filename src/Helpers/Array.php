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
 * Array Helper
 *
 * A collection of helper function to work with array.
 */
// ------------------------------------------------------------------------
if ( ! function_exists('array_get_value')) {
    /**
     * array_get_value
     *
     * Lets you determine whether an array index is set and whether it has a value.
     * If the element is empty it returns NULL (or whatever you specify as the default value).
     *
     * @param string $key
     * @param array  $array
     * @param mixed  $default
     *
     * @return mixed
     */
    function array_get_value($key, array $array, $default = null)
    {
        return array_key_exists($key, $array) ? $array[ $key ] : $default;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_get_values')) {
    /**
     * array_get_values
     *
     * Returns only the array items specified. Will return a default value if
     * it is not set.
     *
     * @param array $keys
     * @param array $array
     * @param null  $default
     *
     * @return array
     */
    function array_get_values(array $keys, array $array, $default = [])
    {
        $return = [];

        is_array($keys) OR $keys = [$keys];

        foreach ($keys as $item) {
            if (array_key_exists($item, $array)) {
                $return[ $item ] = $array[ $item ];
            } elseif (is_array($default) && array_key_exists($item, $default)) {
                $return[ $item ] = $default[ $item ];
            } elseif ( ! empty($default)) {
                $return[ $item ] = $default;
            }
        }

        return $return;

    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_combines')) {
    /**
     * array_combines
     *
     * Creates an array by using one array for keys and another for its values and keep all the values.
     *
     * @param array $keys
     * @param array $values
     *
     * @return array
     */
    function array_combines(array $keys, array $values)
    {
        $combine_array = [];

        foreach ($keys as $index => $key) {
            $combine_array[ $key ][] = $values[ $index ];
        }

        array_walk(
            $combine_array,
            function (&$value) {
                $value = (count($value) == 1) ? array_pop($value) : $value;
            }
        );

        return $combine_array;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_group')) {
    /**
     * array_group
     *
     * Group the array by value or key.
     *
     * @param array $array
     * @param bool  $flip
     *
     * @return array
     */
    function array_group(array $array, $flip = true)
    {
        $group_array = [];

        if ($flip) {
            array_walk_recursive(
                $array,
                function ($value, $key) use (&$group_array) {
                    if ( ! isset($group_array[ $value ]) || ! is_array($group_array[ $value ])) {
                        $group_array[ $value ] = [];
                    }
                    $group_array[ $value ][] = $key;
                }
            );
        } else {
            array_walk_recursive(
                $array,
                function ($value, $key) use (&$group_array) {
                    $group_array[ $key ][] = $value;
                }
            );
        }

        return $group_array;
    }
}

if ( ! function_exists('array_filter_recursive')) {
    /**
     * array_filter_recursive
     *
     * Remove element by the value of array.
     *
     * @param   array $array The array source.
     * @param   mixed $value
     * @param   int   $limit
     *
     * @return  array
     */
    function array_filter_recursive(&$array, $value, $limit = 0)
    {
        if (is_array($value)) {
            foreach ($value as $remove) {
                $array = array_filter_recursive($array, $remove, $limit);
            }

            return $array;
        }

        $result = [];
        $count = 0;

        foreach ($array as $key => $value) {
            if ($count > 0 and $count == $limit) {
                return $result;
            }
            if ( ! is_array($value)) {
                if ($key != $value) {
                    $result[ $key ] = $value;
                    $count++;
                }
            } else {
                $sub = array_filter_recursive($value, $value, $limit);
                if (count($sub) > 0) {
                    if ($key != $value) {
                        $result[ $key ] = $sub;
                        $count += count($sub);
                    }
                }
            }
        }

        return $result;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_search_recursive')) {
    /**
     * array_search_recursive
     *
     * Searches the multidimensional array for a given value and returns the first corresponding key if successful.
     *
     * @param mixed $needle   The searched value.
     * @param array $haystack The multidimensional array.
     * @param bool  $strict
     *
     * @return bool|int|string
     */
    function array_search_recursive($needle, array $haystack, $strict = false)
    {
        $result = '';
        foreach ($haystack as $key => $value) {
            if ($strict === false) {
                $needle = strtolower($needle);

                if ( ! is_array($value)) {
                    $value = strtolower($value);
                } else {
                    $value = array_map('strtolower', $value);
                }
            }

            if ($needle === $value) {
                $result = $key;
                break;
            } elseif (is_array($value)) {
                if (array_search_recursive($needle, $value)) {
                    $result = $key;
                    break;
                }
            }
        }

        return ($result == '') ? false : $result;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_unique_recursive')) {
    /**
     * array_unique_recursive
     *
     * Removes duplicate values from an multidimensional array.
     *
     * @param   array $array Array Source
     *
     * @return  array
     */
    function array_unique_recursive(array $array)
    {
        $serialized = array_map('serialize', $array);
        $unique = array_unique($serialized);

        return array_intersect_key($array, $unique);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_flatten')) {
    /**
     * array_flatten
     *
     * Merge an multidimensional array into regular array.
     *
     * @param array $array
     *
     * @return array
     */
    function array_flatten(array $array = [])
    {
        $flat_array = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flat_array = array_merge($flat_array, array_flatten($value));
            } else {
                $flat_array[ $key ] = $value;
            }
        }

        return $flat_array;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('range_price')) {
    /**
     * range_price
     *
     * Create an array containing a range of prices.
     *
     * @param   int $min        The minimum price numeric value.
     * @param   int $max        The maximum price numeric value.
     * @param   int $multiplier The multiplier used in the range, default is 0.
     *
     * @return  array
     */
    function range_price($min, $max, $multiplier = 0)
    {
        $multiplier = $multiplier * 20;
        $num_range = $max / $min;
        $num_step = $multiplier / $min / 100;

        $ranges = [];
        foreach (range(0, $num_range, $num_step) as $num_price) {
            if ($num_price == 0) {
                $ranges[] = $min;
            } else {
                $ranges[] = $num_price * $min / 2 * 10;
            }
        }

        $prices = [];
        for ($i = 0; $i < count($ranges); $i++) {
            if ($ranges[ $i ] == $max) {
                break;
            } else {
                $prices[ $ranges[ $i ] ] = ($ranges[ $i + 1 ] == 0) ? $ranges[ $i ] * 2 : $ranges[ $i + 1 ];
            }
        }

        return $prices;
    }
}

// ------------------------------------------------------------------------


if ( ! function_exists('range_date')) {
    /**
     * range_date
     *
     * Creates an array containing a range of dates.
     *
     * @param   string|int $start_date Start Date
     * @param   int        $days       Num of days
     *
     * @return  array
     */
    function range_date($start_date, $days = 1)
    {
        $start_date = (is_string($start_date) ? strtotime($start_date) : $start_date);

        $date_range = [];
        for ($i = 0; $i < $days; $i++) {
            $date_range[ $i ] = $start_date + ($i * 24 * 60 * 60);
        }

        return $date_range;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('range_year')) {
    /**
     * range_year
     *
     * Create an array containing a range of years.
     *
     * @param int  $min  The minimum numeric year value.
     * @param null $max  The maximum numeric year value.
     * @param int  $step The increment used in the range, default is 1.
     *
     * @return array
     */
    function range_year($min = 1995, $max = null, $step = 1)
    {
        $max = empty($max) ? date('Y') : $max;

        $years = [];

        foreach (range($min, $max, $step) as $year) {
            $years[ $year ] = $year;
        }

        return $years;
    }
}