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

use O2System\Psr\Patterns\AbstractCollectorPattern;

/**
 * Class Presenter
 *
 * @package O2System\Framework\Http\View
 */
class Presenter extends AbstractCollectorPattern
{
    public function __construct ()
    {
        $this->addItem( 'title', new Presenter\Title() );
        $this->addItem( 'partials', new Presenter\Partials() );
        $this->addItem( 'assets', new Presenter\Assets() );

        if ( $config = config()->loadFile( 'presenter', true ) ) {

            if ( $config->offsetExists( 'assets' ) ) {
                $this->assets->isCombine = $config->offsetGet( 'assets' )->combine;
                $this->assets->loadItems( $config->offsetGet( 'assets' )->autoload );
            }

            if ( $config->offsetExists( 'theme' ) ) {
                $this->setTheme( $config->offsetGet( 'theme' ) );
            }
        }

        // Load Module Assets
        if ( modules()->current()->getConfig()->offsetExists( 'assets' ) ) {
            $this->assets->loadItems( modules()->current()->getConfig()->offsetGet( 'assets' ) );
        }
    }

    public function setTheme ( $themeName )
    {
        if ( is_bool( $themeName ) ) {
            $this->removeItem( 'theme' );
        } elseif ( false !== ( $theme = modules()->current()->getTheme( $themeName ) ) ) {
            // Load Theme Assets
            $this->assets->addFilePath( $theme->getRealPath() );

            if ( $theme->getConfig()->offsetExists( 'assets' ) ) {
                $this->assets->loadItems( $theme->getConfig()->offsetGet( 'assets' ) );
            }

            $this->assets->loadItems(
                [
                    'css' => [ 'theme' ],
                    'js'  => [ 'theme' ],
                ]
            );

            $this->addItem( 'theme', $theme );

            // Load Theme Partials
            foreach ( $theme->getPartials() as $partialName => $partialFileInfo ) {
                $this->partials->addItem( $partialName, $partialFileInfo->getPathName() );
            }
        }
    }

    public function setItem ( $offset, $item )
    {
        if ( $item instanceof \Closure ) {
            parent::addItem( $offset, call_user_func( $item ) );
        } else {
            parent::setItem( $offset, $item );
        }
    }

    public function addItem ( $offset, $item )
    {
        if ( $item instanceof \Closure ) {
            parent::addItem( $offset, call_user_func( $item ) );
        } else {
            parent::addItem( $offset, $item );
        }
    }

    public function loadModel ( $model )
    {
        if ( is_string( $model ) ) {
            if ( class_exists( $model ) ) {
                models()->register( strtolower( get_class_name( $model ) ), new $model() );
            }
        } else {
            models()->register( strtolower( get_class_name( $model ) ), $model );
        }
    }

    public function getArrayCopy ()
    {
        // Add Services
        $this->addItem( 'config', config() );
        $this->addItem( 'language', language() );
        $this->addItem( 'session', session() );
        $this->addItem( 'presenter', presenter() );

        // Add Container
        $this->addItem( 'globals', globals() );

        return parent::getArrayCopy();
    }

    public function &__get ( $property )
    {
        $get[ $property ] = false;

        if ( o2system()->hasService( $property ) ) {
            return o2system()->getService( $property );
        } elseif ( o2system()->__isset( $property ) ) {
            return o2system()->__get( $property );
        } elseif ( property_exists( $this, $property ) ) {
            return $this->{$property};
        } elseif ( $this->hasItem( $property ) ) {
            return $this->getItem( $property );
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    public function __call ( $method, array $args = [ ] )
    {
        if ( method_exists( $this, $method ) ) {
            return call_user_func_array( [ $this, $method ], $args );
        }
    }
}