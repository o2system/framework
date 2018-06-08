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
if ( ! function_exists('get_object_var')) {
    /**
     * array_get_value
     *
     * Lets you determine whether an array index is set and whether it has a value.
     * If the element is empty it returns NULL (or whatever you specify as the default value).
     *
     * @param string $property
     * @param object $object
     * @param mixed  $default
     *
     * @return mixed
     */
    function get_object_var($property, $object, $default = null)
    {
        if(is_object($object)) {
            if(property_exists($object, $property)) {
                return $object->{$property};
            } elseif(method_exists($object, 'has')) {
                if($object->has($property)) {
                    return $object->get($property);
                }
            } elseif(method_exists($object, 'offsetExists')) {
                if($object->offsetExists($property)) {
                    return $object->offsetGet($property);
                }
            } elseif(isset($object->{$property})) {
                return $object->{$property};
            }
        }

        return $default;
    }
}

// ------------------------------------------------------------------------