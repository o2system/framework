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

namespace O2System\Framework\Http\Router\Datastructures;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\Uri\Segments;
use O2System\Spl\Info\SplClassInfo;

/**
 * Class Controller
 *
 * @package O2System\Datastructures
 */
class Controller extends SplClassInfo
{
    private $requestSegments;

    private $requestMethod = null;

    private $requestMethodArgs = [];

    private $properties = [];

    private $instance;

    // ------------------------------------------------------------------------

    public function __construct( $filePath )
    {
        if ( is_object( $filePath ) ) {
            if ( $filePath instanceof \O2System\Framework\Http\Controller ) {
                parent::__construct( $filePath );
                $this->instance = $filePath;
            }
        } elseif ( is_string( $filePath ) && is_file( $filePath ) ) {
            $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
            @list( $namespaceDirectory, $subNamespace ) = explode( 'Controllers', dirname( $filePath ) );

            $classNamespace = loader()->getDirNamespace(
                    $namespaceDirectory
                ) . 'Controllers' . ( empty( $subNamespace ) ? null : str_replace( '/', '\\', $subNamespace ) ) . '\\';
            $className = $classNamespace . $className;

            if ( class_exists( $className ) ) {
                parent::__construct( $className );
            } elseif ( class_exists( '\O2System\Framework\Http\\' . $className ) ) {
                parent::__construct( '\O2System\Framework\Http\\' . $className );
            }
        } elseif ( class_exists( $filePath ) ) {
            parent::__construct( $filePath );
        } elseif ( class_exists( '\O2System\Framework\Http\\' . $filePath ) ) {
            parent::__construct( '\O2System\Framework\Http\\' . $filePath );
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

    public function setRequestSegments( array $segments )
    {
        $this->requestSegments = new Segments( $segments );

        return $this;
    }

    public function getRequestSegments()
    {
        if( empty( $this->requestSegments ) ) {
            $segments[] = $this->getParameter();

            if( ! in_array( $this->getRequestMethod(), [ 'index', 'route' ] ) ) {
                array_push( $segments, $this->getRequestMethod() );
            }

            $this->setRequestSegments( $segments );
        }

        return $this->requestSegments;
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

    public function isValid()
    {
        if ( ! empty( $this->name ) and $this->hasMethod( '__call' ) and $this->isSubclassOf( '\O2System\Framework\Http\Controller' ) ) {
            return true;
        }

        return false;
    }
}