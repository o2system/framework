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

use O2System\Framework\Registries\Module\Theme;
use O2System\Gear\Toolbar;
use O2System\HTML;
use O2System\Spl\Traits\Collectors\FileExtensionCollectorTrait;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class View
 *
 * @package O2System
 */
class View
{
    use FilePathCollectorTrait;
    use FileExtensionCollectorTrait;

    /**
     * View Config
     *
     * @var \O2System\Kernel\Registries\Config
     */
    protected $config;

    /**
     * View HTML Document
     *
     * @var HTML\Document
     */
    protected $document;

    // ------------------------------------------------------------------------

    /**
     * View::__construct
     *
     * @return View
     */
    public function __construct ()
    {
        $this->setFileDirName( 'Views' );
        $this->addFilePath( PATH_APP );

        output()->addFilePath( PATH_APP );

        $this->config = config()->loadFile( 'view', true );

        $this->setFileExtensions(
            [
                '.php',
                '.phtml',
            ]
        );

        if ( $this->config->offsetExists( 'extensions' ) ) {
            $this->setFileExtensions( $this->config[ 'extensions' ] );
        }

        $this->document = new HTML\Document();
    }

    /**
     * __get
     *
     * @param $property
     *
     * @return Parser|bool   Returns FALSE when property is not set.
     */
    public function &__get ( $property )
    {
        $get[ $property ] = false;

        if ( property_exists( $this, $property ) ) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    public function parse ( $string, array $vars = [ ] )
    {
        parser()->loadString( $string );

        return parser()->parse( $vars );
    }

    public function with ( $vars, $value = null )
    {
        if ( isset( $value ) ) {
            $vars = [ $vars => $value ];
        }

        presenter()->mergeItems( $vars );

        return $this;
    }

    public function load ( $filename, array $vars = [ ], $return = false )
    {
        if ( strpos( $filename, 'Pages' ) !== false ) {
            return $this->page( $filename, $vars, $return );
        }

        presenter()->mergeItems( $vars );

        if ( false !== ( $filePath = $this->getFilePath( $filename ) ) ) {
            if ( $return === false ) {

                $partials = presenter()->getItem( 'partials' );

                if ( $partials->hasItem( 'content' ) === false ) {
                    $partials->addItem( 'content', $filePath );
                } else {
                    $partials->addItem( pathinfo( $filePath, PATHINFO_FILENAME ), $filePath );
                }

            } else {
                parser()->loadFile($filePath);
                return parser()->parse( presenter()->getArrayCopy() );
            }
        } else {
            // @todo: throw file not found
        }

        return $this;
    }

    public function page ( $filename, array $vars = [ ], $return = false )
    {
        presenter()->mergeItems( $vars );

        if ( $return === false ) {
            $partials = presenter()->getItem( 'partials' );

            if ( $partials->hasItem( 'content' ) === false ) {
                $partials->addItem( 'content', $filename );
            } else {
                $partials->addItem( pathinfo( $filename, PATHINFO_FILENAME ), $filename );
            }
        } elseif ( parser()->loadFile( $filename ) ) {
            return parser()->parse( presenter()->getArrayCopy() );
        }

        return $this;
    }

    private function getFilePath ( $filename )
    {
        $filename = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $filename );

        if ( is_file( $filename ) ) {
            return realpath( $filename );
        } else {
            $viewsFileExtensions = $this->fileExtensions;
            $viewsDirectories = modules()->getDirs( 'Views' );

            if ( ( $theme = presenter()->getItem( 'theme' ) ) instanceof Theme ) {
                $moduleReplacementPath = $theme->getPathName(
                    ) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . dash(
                                             modules()->current()->getDirName()
                                         ) . DIRECTORY_SEPARATOR;

                if ( is_dir( $moduleReplacementPath ) ) {
                    array_unshift( $viewsDirectories, $moduleReplacementPath );

                    // Add Theme File Extensions
                    if ( $theme->getConfig()->offsetExists( 'extension' ) ) {
                        array_unshift( $viewsFileExtensions, $theme->getConfig()->offsetGet( 'extension' ) );
                    } elseif ( $theme->getConfig()->offsetExists( 'extensions' ) ) {
                        $viewsFileExtensions = array_merge(
                            $theme->getConfig()->offsetGet( 'extensions' ),
                            $viewsFileExtensions
                        );
                    }

                    // Add Theme Parser Engine
                    if ( $theme->getConfig()->offsetExists( 'driver' ) ) {
                        $parserDriverClassName = '\O2System\Parser\Drivers\\' . camelcase(
                                $theme->getConfig()->offsetGet( 'driver' )
                            );

                        if ( class_exists( $parserDriverClassName ) ) {
                            parser()->addDriver(
                                new $parserDriverClassName(),
                                $theme->getConfig()->offsetGet( 'driver' )
                            );
                        }
                    }
                }
            }

            foreach ( $viewsDirectories as $viewsDirectory ) {
                foreach ( $viewsFileExtensions as $fileExtension ) {
                    $filename = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $filename );

                    if ( is_file( $filePath = $viewsDirectory . $filename . $fileExtension ) ) {
                        return realpath( $filePath );
                        break;
                    }
                }
            }
        }

        return false;
    }

    public function render ( $return = false )
    {
        parser()->loadVars( presenter()->getArrayCopy() );

        $this->document->title->text( presenter()->title->browser->__toString() );

        if ( ( $theme = presenter()->getItem( 'theme' ) ) instanceof Theme ) {
            parser()->loadFile( $theme->getLayout() );
            $this->document->loadHTML( parser()->parse() );
        } else {
            $this->document->find( 'body' )->append( presenter()->partials->__get( 'content' ) );
        }

        if ( input()->env( 'DEBUG_STAGE' ) === 'DEVELOPER' ) {
            $this->document->find( 'body' )->prepend( ( new Toolbar() )->__toString() );
        }

        if ( $return === true ) {
            return $this->document->saveHTML();
        }

        output()->show( $this->document->saveHTML() );
    }
}