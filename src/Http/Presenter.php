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

use O2System\Psr\Patterns\AbstractVariableStoragePattern;

/**
 * Class Presenter
 *
 * @package O2System\Framework\Http\View
 */
class Presenter extends AbstractVariableStoragePattern
{
    /**
     * Presenter::__construct
     */
    public function __construct()
    {
        $this->store( 'title', new Presenter\Title() );
        $this->store( 'partials', new Presenter\Partials() );
        $this->store( 'assets', new Presenter\Assets() );

        if ( $config = config()->loadFile( 'presenter', true ) ) {

            if ( $config->offsetExists( 'assets' ) ) {
                $this->storage[ 'assets' ]->isCombine = $config->offsetGet( 'assets' )->combine;
                $this->storage[ 'assets' ]->loadFiles( $config->offsetGet( 'assets' )->autoload );
            }

            if ( $config->offsetExists( 'theme' ) ) {
                $this->setTheme( $config->offsetGet( 'theme' ) );
            }

            if ( $config->offsetExists( 'debugToolBar' ) ) {
                $this->setDebugToolBar( $config->offsetGet( 'debugToolBar' ) );
            } elseif ( input()->env( 'DEBUG_STAGE' ) === 'DEVELOPER' ) {
                $this->setDebugToolBar( true );
            } else {
                $this->setDebugToolBar( false );
            }
        }

        // Load Module Assets
        if ( modules()->current()->getConfig()->offsetExists( 'assets' ) ) {
            $this->storage[ 'assets' ]->loadFiles( modules()->current()->getConfig()->offsetGet( 'assets' ) );
        }
    }

    public function setTheme( $themeName )
    {
        if ( is_bool( $themeName ) ) {
            $this->remove( 'theme' );
        } elseif ( false !== ( $theme = modules()->current()->getTheme( $themeName ) ) ) {
            // Load Theme Assets
            $this->storage[ 'assets' ]->addFilePath( $theme->getRealPath() );

            if ( $theme->getConfig()->offsetExists( 'assets' ) ) {
                $this->storage[ 'assets' ]->loadFiles( $theme->getConfig()->offsetGet( 'assets' ) );
            }

            $this->storage[ 'assets' ]->loadFiles(
                [
                    'css' => [ 'theme' ],
                    'js'  => [ 'theme' ],
                ]
            );

            $this->store( 'theme', $theme );

            // Load Theme Partials
            foreach ( $theme->getPartials() as $partialName => $partialFileInfo ) {
                $this->storage[ 'partials' ]->addPartial( $partialName, $partialFileInfo->getPathName() );
            }
        }
    }

    public function setDebugToolBar( $debugToolBar )
    {
        if ( is_bool( $debugToolBar ) ) {
            $this->addItem( 'debugToolBar', $debugToolBar );
        }

        return $this;
    }

    public function addItem( $offset, $item )
    {
        if ( $item instanceof \Closure ) {
            parent::store( $offset, call_user_func( $item, $this ) );
        } else {
            parent::store( $offset, $item );
        }
    }

    public function setThemeLayout( $themeLayout )
    {
        if ( false !== ( $theme = $this->offsetGet( 'theme' ) ) ) {

            $theme->setLayout( $themeLayout );

            // Load Theme Layout Assets
            $layoutFilePath = $theme->getRealPath() . 'layouts' . DIRECTORY_SEPARATOR . $themeLayout . DIRECTORY_SEPARATOR;
            $this->storage[ 'assets' ]->addFilePath( $layoutFilePath );

            $this->storage[ 'assets' ]->loadFiles(
                [
                    'css' => [ 'layout' ],
                    'js'  => [ 'layout' ],
                ]
            );

            $this->store( 'theme', $theme );

            // Load Theme Partials
            foreach ( $theme->getPartials() as $partialName => $partialFileInfo ) {
                $this->storage[ 'partials' ]->addPartial( $partialName, $partialFileInfo->getPathName() );
            }
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

    public function getArrayCopy()
    {
        $storage = $this->storage;

        // Add Services
        $storage[ 'config' ] = config();
        $storage[ 'language' ] = language();
        $storage[ 'session' ] = session();
        $storage[ 'presenter' ] = presenter();

        // Add Container
        $storage[ 'globals' ] = globals();

        return $storage;
    }

    public function &__get( $property )
    {
        if ( o2system()->hasService( $property ) ) {
            return o2system()->getService( $property );
        } elseif ( o2system()->__isset( $property ) ) {
            return o2system()->__get( $property );
        } elseif ( property_exists( $this, $property ) ) {
            return $this->{$property};
        }

        return parent::__get( $property );
    }

    // ------------------------------------------------------------------------

    public function __call( $method, array $args = [] )
    {
        if ( method_exists( $this, $method ) ) {
            return call_user_func_array( [ $this, $method ], $args );
        }
    }
}