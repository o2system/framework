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

namespace O2System\Framework\Cli\Router\Datastructures;

// ------------------------------------------------------------------------

use O2System\Spl\Info\SplClassInfo;

/**
 * Class Controller
 *
 * @package O2System\Datastructures
 */
class Commander extends SplClassInfo
{
    private $requestMethod = null;


    private $requestMethodArgs = [];

    private $properties;

    private $instance;

    // ------------------------------------------------------------------------

    public function __construct( $filePath )
    {
        if ( is_object( $filePath ) ) {
            if ( $filePath instanceof \O2System\Framework\Cli\Commander ) {
                parent::__construct( $filePath );
                $this->instance = $filePath;
            }
        } elseif ( is_string( $filePath ) && is_file( $filePath ) ) {
            $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
            @list( $namespaceDirectory, $subNamespace ) = explode( 'Commanders', dirname( $filePath ) );
            $classNamespace = loader()->getDirNamespace(
                    $namespaceDirectory
                ) . 'Commanders' . ( empty( $subNamespace ) ? null : str_replace( '/', '\\', $subNamespace ) ) . '\\';
            $className = $classNamespace . $className;

            if ( class_exists( $className ) ) {
                parent::__construct( $className );
            } elseif ( class_exists( '\O2System\Framework\Cli\\' . $className ) ) {
                parent::__construct( '\O2System\Framework\Cli\\' . $className );
            }
        }
    }

    // ------------------------------------------------------------------------

    public function setProperties( array $properties )
    {
        $this->properties = $properties;
    }

    // ------------------------------------------------------------------------

    public function getParameter()
    {
        return strtolower( get_class_name( $this->name ) );
    }

    // ------------------------------------------------------------------------

    public function &getInstance()
    {
        if ( empty( $this->instance ) ) {
            $className = $this->name;
            $this->instance = new $className();

            if ( count( $this->properties ) ) {
                foreach ( $this->properties as $key => $value ) {
                    $setterMethodName = camelcase( 'set_' . $key );

                    if ( method_exists( $this->instance, $setterMethodName ) ) {
                        $this->instance->{$setterMethodName}( $value );
                    } else {
                        $this->instance->{$key} = $value;
                    }
                }
            }
        }

        return $this->instance;
    }

    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    // ------------------------------------------------------------------------

    public function setRequestMethod( $method )
    {
        $this->requestMethod = $method;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function getRequestMethodArgs()
    {
        return $this->requestMethodArgs;
    }

    public function setRequestMethodArgs( array $arguments )
    {
        $arguments = array_values( $arguments );
        array_unshift( $arguments, null );
        unset( $arguments[ 0 ] );

        $this->requestMethodArgs = $arguments;

        return $this;
    }
}