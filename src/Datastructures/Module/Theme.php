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

namespace O2System\Framework\Datastructures\Module;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Theme
 *
 * @package O2System\Framework\Datastructures\Metadata
 */
class Theme extends SplDirectoryInfo
{
    /**
     * Theme Properties
     *
     * @var array
     */
    private $properties = [];

    /**
     * Theme Config
     *
     * @var array
     */
    private $config = [];

    /**
     * Theme Layout
     *
     * @var SplFileInfo
     */
    private $layout;

    /**
     * Theme Partials
     *
     * @var array
     */
    private $partials = [];

    /**
     * Theme::__construct
     *
     * @param string $dir
     */
    public function __construct( $dir )
    {
        parent::__construct( $dir );

        // Set Theme Properties
        if ( is_file( $propFilePath = $dir . 'theme.jsprop' ) ) {
            $properties = json_decode( file_get_contents( $propFilePath ), true );

            if ( json_last_error() === JSON_ERROR_NONE ) {
                $this->properties = $properties;
            }
        }

        // Set Theme Config
        if ( is_file( $propFilePath = $dir . 'theme.jsconf' ) ) {
            $config = json_decode( file_get_contents( $propFilePath ), true );

            if ( json_last_error() === JSON_ERROR_NONE ) {
                $this->config = $config;
            }
        }

        // Set Default Theme Layout
        $this->setLayout( 'theme' );
    }

    public function isValid()
    {
        if ( count( $this->properties ) ) {
            return true;
        }

        return false;
    }

    public function getParameter()
    {
        return $this->getDirName();
    }

    public function getCode()
    {
        return strtoupper( substr( md5( $this->getDirName() ), 2, 7 ) );
    }

    public function getChecksum()
    {
        return md5( $this->getMTime() );
    }

    public function getProperties()
    {
        return new SplArrayObject( $this->properties );
    }

    public function getConfig()
    {
        return new SplArrayObject( $this->config );
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout( $layout )
    {
        $extensions = [ '.php', '.phtml', '.html', '.tpl' ];

        if ( isset( $this->config[ 'extensions' ] ) ) {
            array_unshift( $partialsExtensions, $this->config[ 'extension' ] );
        } elseif ( isset( $this->config[ 'extension' ] ) ) {
            array_unshift( $extensions, $this->config[ 'extension' ] );
        }

        foreach ( $extensions as $extension ) {
            if ( $layout === 'theme' ) {
                $layoutFilePath = $this->getRealPath() . 'theme.' . trim( $extension, '.' );
            } else {
                $layoutFilePath = $this->getRealPath() . 'layouts' . DIRECTORY_SEPARATOR . dash( $layout ) . DIRECTORY_SEPARATOR . 'layout.' . trim( $extension,
                        '.' );
            }

            if ( is_file( $layoutFilePath ) ) {
                $this->layout = new SplFileInfo( $layoutFilePath );
                $this->loadPartials();

                break;
            }
        }

        // Load Layout Partials
        if ( empty( $this->layout ) ) {
            // @todo throw new exception layout not found
        }

        return $this;
    }

    public function getPartials()
    {
        return new SplArrayObject( $this->partials );
    }

    protected function loadPartials()
    {
        $this->partials = [];
        if ( ! $this->layout instanceof SplFileInfo ) {
            return;
        }

        if ( is_dir(
            $partialsFilePath = $this->layout->getPath() . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR
        ) ) {

            $partialsExtensions = [ '.php', '.phtml', '.html', '.tpl' ];

            if ( isset( $this->config[ 'extension' ] ) ) {
                array_unshift( $partialsExtensions, $this->config[ 'extension' ] );
            } elseif ( isset( $this->config[ 'extensions' ] ) ) {
                $partialsExtensions = $this->config[ 'extensions' ];
            }

            $partialsFiles = scandir( $partialsFilePath );
            $partialsFiles = array_slice( $partialsFiles, 2 );

            foreach ( $partialsFiles as $partialsFile ) {
                $filePath = $partialsFilePath . $partialsFile;

                if ( is_file( $filePath ) and in_array( '.' . pathinfo( $filePath, PATHINFO_EXTENSION ),
                        $partialsExtensions )
                ) {
                    $fileKey = str_replace( $partialsExtensions, '', $partialsFile );
                    $this->partials[ $fileKey ] = new SplFileInfo( $filePath );
                }
            }
        }
    }
}