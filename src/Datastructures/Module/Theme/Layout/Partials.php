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

namespace O2System\Framework\Datastructures\Module\Theme\Layout;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\AbstractItemStoragePattern;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Partials
 *
 * @package O2System\Framework\Datastructures\Module\Theme\Layout
 */
class Partials extends AbstractItemStoragePattern
{
    protected $path;
    protected $extension;

    public function setPath( $path )
    {
        if ( is_dir( $path ) ) {
            $this->path = $path;
        }

        return $this;
    }

    public function setExtension( $extension )
    {
        $this->extension = '.' . rtrim( $extension, '.' );

        return $this;
    }

    public function autoload()
    {
        if( is_dir( $this->path ) ) {
            $this->loadDir( $this->path );
        }
    }

    public function loadDir( $dir )
    {
        $partialsFiles = scandir( $dir );
        $partialsFiles = array_slice( $partialsFiles, 2 );

        foreach ( $partialsFiles as $partialsFile ) {

            $partialsFilePath = $this->path . $partialsFile;

            if ( is_file( $partialsFilePath ) ) {
                $this->loadFile( $partialsFilePath );
            } elseif ( is_dir( $partialsFilePath ) ) {
                $this->loadDir( $partialsFilePath );
            }
        }
    }

    public function loadFile( $filePath )
    {
        if ( strrpos( $filePath, $this->extension ) !== false ) {
            $fileKey = str_replace( $this->extension, '', pathinfo( $filePath, PATHINFO_BASENAME ) );
            $fileKey = isset( $partialsKey ) ? $partialsKey . '-' . $fileKey : $fileKey;
            $this->store( camelcase( $fileKey ), new SplFileInfo( $filePath ) );
        }
    }

    public function hasPartial( $partialOffset )
    {
        return $this->__isset( $partialOffset );
    }

    public function &__get( $partial )
    {
        $partialContent = parent::__get( $partial );

        if ( is_file( $partialContent ) ) {
            parser()->loadFile( $partialContent );

            return parser()->parse();
        } elseif ( is_string( $partialContent ) ) {
            return $partialContent;
        }

        return null;
    }
}