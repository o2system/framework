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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

/**
 * Class Controller
 *
 * @package O2System\Framework\Http
 */
class Controller
{
    public function &__get( $property )
    {
        $get[ $property ] = false;

        if ( o2system()->hasService( $property ) ) {
            return o2system()->getService( $property );
        } elseif ( o2system()->__isset( $property ) ) {
            return o2system()->__get( $property );
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    public function __call( $method, array $args = [] )
    {
        if ( method_exists( $this, $method ) ) {
            return call_user_func_array( [ $this, $method ], $args );
        }
    }

    public function loadModel( $model )
    {
        if ( is_string( $model ) ) {
            if ( class_exists( $model ) ) {
                models()->register( strtolower( get_class_name( $model ) ), new $model() );
            }
        } else {
            models()->register( strtolower( get_class_name( $model ) ), $model );
        }
    }

    public function loadPresenter( $presenter, $importVars = true )
    {
        if ( is_string( $presenter ) ) {
            $presenter = new $presenter();
        }

        // Merge presenter variables
        if ( $presenter instanceof Presenter ) {
            if ( $importVars ) {
                $presenter->mergeItems( presenter()->getArrayCopy() );
            }

            // replace current presenter
            o2system()->addService( $presenter, 'presenter' );
        }
    }
}