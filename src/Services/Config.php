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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\AbstractVariableStoragePattern;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Config
 *
 * @package O2System\Framework\Services
 */
class Config extends AbstractVariableStoragePattern
{
    /**
     * Config::loadFile
     *
     * @param string $offset
     * @param bool   $return
     *
     * @return mixed
     */
    public function loadFile( $offset, $return = false )
    {
        $configFile = studlycase( $offset );

        $configDirs = [
            PATH_FRAMEWORK . 'Config' . DIRECTORY_SEPARATOR,
            PATH_APP . 'Config' . DIRECTORY_SEPARATOR,
        ];

        if ( method_exists( modules(), 'getDirs' ) ) {
            $configDirs = modules()->getDirs( 'Config', true );
        }

        foreach ( $configDirs as $configDir ) {
            if ( is_file(
                $filePath = $configDir . ucfirst(
                        strtolower( ENVIRONMENT )
                    ) . DIRECTORY_SEPARATOR . $configFile . '.php'
            ) ) {
                include( $filePath );
            } elseif ( is_file( $filePath = $configDir . DIRECTORY_SEPARATOR . $configFile . '.php' ) ) {
                include( $filePath );
            }
        }

        if ( isset( $$offset ) ) {
            $this->addItem( $offset, $$offset );

            unset( $$offset );

            if ( $return ) {
                return $this->getItem( $offset );
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function addItem( $offset, $value )
    {
        $this->store( $offset, $value );
    }

    /**
     * Config::getItem
     *
     * @param $offset
     *
     * @return mixed|\O2System\Spl\Datastructures\SplArrayObject
     */
    public function &getItem( $offset )
    {
        $item = parent::getVariable( $offset );

        if ( is_array( $item ) ) {
            if ( is_string( key( $item ) ) ) {
                $item = new SplArrayObject( $item );
            }
        }

        return $item;
    }

    public function setItem( $offset, $value )
    {
        $this->store( $offset, $value );
    }
}