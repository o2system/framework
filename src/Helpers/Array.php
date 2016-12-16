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

if ( ! function_exists( 'array_get_value' ) ) {
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
    function array_get_value ( $key, array $array, $default = null )
    {
        return array_key_exists( $key, $array ) ? $array[ $key ] : $default;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'array_get_values' ) ) {
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
    function array_get_values ( array $keys, array $array, $default = null )
    {
        $return = [ ];

        is_array( $keys ) OR $keys = [ $keys ];

        foreach ( $keys as $item ) {
            $return[ $item ] = array_key_exists( $item, $array ) ? $array[ $item ] : $default;
        }

        return $return;

    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'array_is_multidimensional' ) ) {
    /**
     * array_is_multidimensional
     *
     * Checks if the given array is multidimensional.
     *
     * @param array $array
     *
     * @return bool
     */
    function array_is_multidimensional ( array $array )
    {
        if ( count( $array ) != count( $array, COUNT_RECURSIVE ) ) {
            return true;
        }

        return false;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'array_is_associative' ) ) {
    /**
     * array_is_associative
     *
     * Check if the given array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    function array_is_associative ( array $array )
    {
        if ( $array == [ ] ) {
            return true;
        }
        $keys = array_keys( $array );
        if ( array_keys( $keys ) !== $keys ) {
            foreach ( $keys as $key ) {
                if ( ! is_numeric( $key ) ) {
                    return true;
                }
            }
        }

        return false;
    }
}
if ( ! function_exists( 'array_is_indexed' ) ) {
    /**
     * array_is_indexed
     *
     * Check if an array has a numeric index.
     *
     * @param array $array
     *
     * @return bool
     */
    function array_is_indexed ( array $array )
    {
        if ( $array == [ ] ) {
            return true;
        }

        return ! array_is_associative( $array );
    }
}

if ( ! function_exists( 'array_combines' ) ) {
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
    function array_combines ( array $keys, array $values )
    {
        $combine_array = [ ];

        foreach ( $keys as $index => $key ) {
            $combine_array[ $key ][] = $values[ $index ];
        }

        array_walk(
            $combine_array,
            function ( &$value ) {
                $value = ( count( $value ) == 1 ) ? array_pop( $value ) : $value;
            }
        );

        return $combine_array;
    }

}

if ( ! function_exists( 'array_group' ) ) {
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
    function array_group ( array $array, $flip = true )
    {
        $group_array = [ ];

        if ( $flip ) {
            array_walk_recursive(
                $array,
                function ( $value, $key ) use ( &$group_array ) {
                    if ( ! isset( $group_array[ $value ] ) || ! is_array( $group_array[ $value ] ) ) {
                        $group_array[ $value ] = [ ];
                    }
                    $group_array[ $value ][] = $key;
                }
            );
        } else {
            array_walk_recursive(
                $array,
                function ( $value, $key ) use ( &$group_array ) {
                    $group_array[ $key ][] = $value;
                }
            );
        }

        return $group_array;
    }
}

if ( ! function_exists( 'array_filter_recursive' ) ) {
    /**
     * Recursive Filter Array Value
     *
     * Remove element by the value of array
     *
     * @param   array $array Array Source
     * @param   mixed $value
     * @param   int   $limit
     *
     * @return  array
     */
    function array_filter_recursive ( &$array, $value, $limit = 0 )
    {
        if ( is_array( $value ) ) {
            foreach ( $value as $remove ) {
                $array = array_filter_recursive( $array, $remove, $limit );
            }

            return $array;
        }

        $result = [ ];
        $count = 0;

        foreach ( $array as $key => $value ) {
            if ( $count > 0 and $count == $limit ) {
                return $result;
            }
            if ( ! is_array( $value ) ) {
                if ( $key != $value ) {
                    $result[ $key ] = $value;
                    $count++;
                }
            } else {
                $sub = array_filter_recursive( $value, $value, $limit );
                if ( count( $sub ) > 0 ) {
                    if ( $key != $value ) {
                        $result[ $key ] = $sub;
                        $count += count( $sub );
                    }
                }
            }
        }

        return $result;
    }
}
// ------------------------------------------------------------------------


if ( ! function_exists( 'array_search_recursive' ) ) {
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
    function array_search_recursive ( $needle, array $haystack, $strict = false )
    {
        $result = '';
        foreach ( $haystack as $key => $value ) {
            if ( $strict === false ) {
                $needle = strtolower( $needle );

                if ( ! is_array( $value ) ) {
                    $value = strtolower( $value );
                } else {
                    $value = array_map( 'strtolower', $value );
                }
            }

            if ( $needle === $value ) {
                $result = $key;
                break;
            } elseif ( is_array( $value ) ) {
                if ( array_search_recursive( $needle, $value ) ) {
                    $result = $key;
                    break;
                }
            }
        }

        return ( $result == '' ) ? false : $result;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'array_unique_recursive' ) ) {
    /**
     * array_unique_recursive
     *
     * Removes duplicate values from an multidimensional array.
     *
     * @param   array $array Array Source
     *
     * @return  array
     */
    function array_unique_recursive ( array $array )
    {
        $serialized = array_map( 'serialize', $array );
        $unique = array_unique( $serialized );

        return array_intersect_key( $array, $unique );
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'array_flatten' ) ) {
    /**
     * array_flatten
     *
     * Merge an multidimensional array into regular array.
     *
     * @param array $array
     *
     * @return array
     */
    function array_flatten ( array $array = [ ] )
    {
        $flat_array = [ ];

        foreach ( $array as $key => $value ) {
            if ( is_array( $value ) ) {
                $flat_array = array_merge( $flat_array, array_flatten( $value ) );
            } else {
                $flat_array[ $key ] = $value;
            }
        }

        return $flat_array;
    }
}