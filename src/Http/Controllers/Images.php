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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;
use O2System\Image\Manipulation;

/**
 * Class Images
 *
 * @package O2System\Framework\Http\Controllers
 */
class Images extends Controller
{
    public $imagesPath;
    public $imagesNotFound = 'not-found.jpg';

    public function __construct()
    {
        $this->imagesPath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR;
    }

    public function reRoute()
    {
        $segments = array_merge( [ func_get_arg( 0 ) ], func_get_arg( 1 ) );
        $filePath = $this->imagesNotFound = $this->imagesPath . 'not-found.jpg';

        if ( count( $segments ) == 1 ) {
            $filePath = $this->imagesPath . end( $segments );
        } elseif ( count( $segments ) >= 2 ) {
            if ( preg_match( "/(\d+)(x)(\d+)/", $segments[ count( $segments ) - 2 ], $matches ) ) {
                $size[ 'width' ] = $matches[ 1 ];
                $size[ 'height' ] = $matches[ 3 ];

                if ( count( $segments ) == 2 ) {
                    $filePath = $this->imagesPath . end( $segments );
                } else {
                    $filePath = $this->imagesPath . implode( DIRECTORY_SEPARATOR,
                            array_slice( $segments, 0,
                                count( $segments ) - 2 ) ) . DIRECTORY_SEPARATOR . end( $segments );
                }
            } else {
                $filePath = $this->imagesPath . implode( DIRECTORY_SEPARATOR, $segments );
            }
        }

        if ( ! is_file( $filePath ) ) {
            $filePath = $this->imagesNotFound;
        }

        if ( empty( $size ) ) {
            $this->original( $filePath );
        } else {
            $this->resize( $filePath, $size );
        }
    }

    protected function original( $filePath )
    {
        $manipulation = new Manipulation( config( 'image', true ) );
        $manipulation->setImageFile( $filePath );
        $manipulation->displayImage();
    }

    protected function resize( $filePath, $size )
    {
        $manipulation = new Manipulation( config( 'image', true ) );
        $manipulation->setImageFile( $filePath );
        $manipulation->resizeImage( $size[ 'width' ], $size[ 'height' ] );
        $manipulation->displayImage();
    }
}