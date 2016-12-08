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

namespace O2System\Framework\Http\Message;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\ServerRequest;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Input
 *
 * @package O2System\Framework\Http\Message
 */
class Input
{
    public function getPost ( $offset, $filter = null )
    {
        // Use $_GET directly here, since filter_has_var only
        // checks the initial GET data, not anything that might
        // have been added since.
        return isset( $_GET[ $offset ] )
            ? $this->get( $offset, $filter )
            : $this->post( $offset, $filter );
    }

    // ------------------------------------------------------------------------

    public function get ( $offset = null, $filter = null )
    {
        return $this->filter( INPUT_GET, $offset, $filter );
    }

    // ------------------------------------------------------------------------

    public function filter ( $type, $offset = null, $filter = FILTER_DEFAULT )
    {
        // If $offset is null, it means that the whole input type array is requested
        if ( is_null( $offset ) ) {
            $loopThrough = [ ];
            switch ( $type ) {
                case INPUT_GET    :
                    $loopThrough = $_GET;
                    break;
                case INPUT_POST   :
                    $loopThrough = $_POST;
                    break;
                case INPUT_COOKIE :
                    $loopThrough = $_COOKIE;
                    break;
                case INPUT_SERVER :
                    $loopThrough = $_SERVER;
                    break;
                case INPUT_ENV    :
                    $loopThrough = $_ENV;
                    break;
            }

            $values = [ ];
            foreach ( $loopThrough as $key => $value ) {
                if ( is_array( $value ) AND is_array( $filter ) ) {
                    $values[ $key ] = filter_var_array( $value, $filter );
                } elseif ( is_array( $value ) ) {
                    $values[ $key ] = $this->filterRecursive( $value, $filter );
                } else {
                    $values[ $key ] = filter_var( $value, $filter );
                }
            }

            if ( empty( $values ) ) {
                return false;
            }

            return new SplArrayObject( $values );
        }

        // allow fetching multiple keys at once
        if ( is_array( $offset ) ) {
            $output = [ ];

            foreach ( $offset as $key ) {
                $output[ $key ] = $this->filter( $type, $key, $filter );
            }

            return $output;
        }

        // Due to issues with FastCGI and testing,
        // we need to do these all manually instead
        // of the simpler filter_input();
        switch ( $type ) {
            case INPUT_GET:
                $value = isset( $_GET[ $offset ] ) ? $_GET[ $offset ] : null;
                break;
            case INPUT_POST:
                $value = isset( $_POST[ $offset ] ) ? $_POST[ $offset ] : null;
                break;
            case INPUT_SERVER:
                $value = isset( $_SERVER[ $offset ] ) ? $_SERVER[ $offset ] : null;
                break;
            case INPUT_ENV:
                $value = isset( $_ENV[ $offset ] ) ? $_ENV[ $offset ] : null;
                break;
            case INPUT_COOKIE:
                $value = isset( $_COOKIE[ $offset ] ) ? $_COOKIE[ $offset ] : null;
                break;
            case INPUT_REQUEST:
                $value = isset( $_REQUEST[ $offset ] ) ? $_REQUEST[ $offset ] : null;
                break;
            case INPUT_SESSION:
                $value = isset( $_SESSION[ $offset ] ) ? $_SESSION[ $offset ] : null;
                break;
            default:
                $value = '';
        }

        if ( is_array( $value ) ) {
            if ( is_string( key( $value ) ) ) {
                return new SplArrayObject( $value );
            } else {
                return $value;
            }
        } elseif ( is_object( $value ) ) {
            return $value;
        }

        return filter_var( $value, $filter );
    }

    // ------------------------------------------------------------------------

    private function filterRecursive ( $loopThrough, $filter = null )
    {
        $values = [ ];

        foreach ( $loopThrough as $key => $value ) {
            if ( is_array( $value ) AND is_array( $filter ) ) {
                $values[ $key ] = filter_var_array( $value, $filter );
            } elseif ( is_array( $value ) ) {
                $values[ $key ] = $this->filterRecursive( $value, $filter );
            } elseif ( isset( $filter ) ) {
                $values[ $key ] = filter_var( $value, $filter );
            } else {
                $values[ $key ] = $value;
            }
        }

        return $values;
    }

    // ------------------------------------------------------------------------

    public function post ( $offset = null, $filter = null )
    {
        return $this->filter( INPUT_POST, $offset, $filter );
    }

    // ------------------------------------------------------------------------

    public function postGet ( $offset, $filter = null )
    {
        // Use $_POST directly here, since filter_has_var only
        // checks the initial POST data, not anything that might
        // have been added since.
        return isset( $_POST[ $offset ] )
            ? $this->post( $offset, $filter )
            : $this->get( $offset, $filter );
    }

    public function files ( $offset = null )
    {
        static $serverRequest;

        if ( empty( $serverRequest ) ) {
            $serverRequest = new ServerRequest();
        }

        $uploadFiles = $serverRequest->getUploadedFiles();

        if ( isset( $offset ) ) {
            if ( isset( $uploadFiles[ $offset ] ) ) {
                return $uploadFiles[ $offset ];
            }
        }

        return $uploadFiles;
    }

    public function env ( $offset = null, $filter = null )
    {
        return $this->filter( INPUT_ENV, $offset, $filter );
    }

    //--------------------------------------------------------------------

    public function cookie ( $offset = null, $filter = null )
    {
        return $this->filter( INPUT_COOKIE, $offset, $filter );
    }

    // ------------------------------------------------------------------------

    public function server ( $offset = null, $filter = null )
    {
        return $this->filter( INPUT_SERVER, $offset, $filter );
    }

    //--------------------------------------------------------------------

    /**
     * Is Post Insert
     *
     * Is a insert post form request
     *
     * @access  public
     * @return  boolean
     */
    public function isPostInsert ()
    {
        if ( empty( $_POST[ 'id' ] ) AND
             (
                 isset( $_POST[ 'add' ] ) OR
                 isset( $_POST[ 'add_new' ] ) OR
                 isset( $_POST[ 'add_as_new' ] ) OR
                 isset( $_POST[ 'save' ] )
             )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Is Post Update
     *
     * Is a update post form request
     *
     * @access  public
     * @return  boolean
     */
    public function isPostUpdate ()
    {
        if ( ! empty( $_POST[ 'id' ] ) AND
             (
                 isset( $_POST[ 'update' ] ) OR
                 isset( $_POST[ 'save' ] )
             )
        ) {
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------
}