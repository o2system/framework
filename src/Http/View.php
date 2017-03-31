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

use O2System\Framework\Http\Presenter\Title;
use O2System\Framework\Http\Router\Datastructures\Page;
use O2System\Framework\Datastructures\Module\Theme;
use O2System\Gear\Toolbar;
use O2System\Html;
use O2System\Spl\Exceptions\ErrorException;
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
     * @var \O2System\Kernel\Datastructures\Config
     */
    protected $config;

    /**
     * View HTML Document
     *
     * @var Html\Document
     */
    protected $document;

    // ------------------------------------------------------------------------

    /**
     * View::__construct
     *
     * @return View
     */
    public function __construct()
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

        $this->document = new Html\Document();
    }

    /**
     * __get
     *
     * @param $property
     *
     * @return Parser|bool   Returns FALSE when property is not set.
     */
    public function &__get( $property )
    {
        $get[ $property ] = false;

        if ( property_exists( $this, $property ) ) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    public function parse( $string, array $vars = [] )
    {
        parser()->loadString( $string );

        return parser()->parse( $vars );
    }

    public function with( $vars, $value = null )
    {
        if ( isset( $value ) ) {
            $vars = [ $vars => $value ];
        }

        presenter()->merge( $vars );

        return $this;
    }

    public function load( $filename, array $vars = [], $return = false )
    {
        if ( $filename instanceof Page ) {
            return $this->page( $filename->getRealPath(), array_merge( $vars, $filename->getVars() ) );
        }

        if ( strpos( $filename, 'Pages' ) !== false ) {
            return $this->page( $filename, $vars, $return );
        }

        presenter()->merge( $vars );

        if ( false !== ( $filePath = $this->getFilePath( $filename ) ) ) {

            if ( $return === false ) {

                $partials = presenter()->getVariable( 'partials' );

                if ( $partials->hasPartial( 'content' ) === false ) {
                    $partials->addPartial( 'content', $filePath );
                } else {
                    $partials->addPartial( pathinfo( $filePath, PATHINFO_FILENAME ), $filePath );
                }

            } else {
                parser()->loadFile( $filePath );

                return parser()->parse( presenter()->getArrayCopy() );
            }
        } else {

            $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );

            $error = new ErrorException(
                'E_VIEW_NOT_FOUND',
                0,
                $backtrace[ 0 ][ 'file' ],
                $backtrace[ 0 ][ 'line' ],
                [ trim( $filename ) ]
            );

            unset( $backtrace );

            ob_start();
            include PATH_KERNEL . 'Views' . DIRECTORY_SEPARATOR . 'http' . DIRECTORY_SEPARATOR . 'error.phtml';
            $content = ob_get_contents();
            ob_end_clean();

            if ( $return === false ) {

                $partials = presenter()->getVariable( 'partials' );

                if ( $partials->hasPartial( 'content' ) === false ) {
                    $partials->addPartial( 'content', $content );
                } else {
                    $partials->addPartial( pathinfo( $filePath, PATHINFO_FILENAME ), $content );
                }

            } else {
                return $content;
            }
        }
    }

    public function page( $filename, array $vars = [], $return = false )
    {
        if ( $filename instanceof Page ) {
            return $this->page( $filename->getRealPath(), array_merge( $vars, $filename->getVars() ) );
        }

        presenter()->merge( $vars );

        if ( $return === false ) {
            $partials = presenter()->getVariable( 'partials' );

            if ( $partials->hasPartial( 'content' ) === false ) {
                $partials->addPartial( 'content', $filename );
            } else {
                $partials->addPartial( pathinfo( $filename, PATHINFO_FILENAME ), $filename );
            }
        } elseif ( parser()->loadFile( $filename ) ) {
            return parser()->parse( presenter()->getArrayCopy() );
        }
    }

    private function getFilePath( $filename )
    {
        $filename = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $filename );

        if ( is_file( $filename ) ) {
            return realpath( $filename );
        } else {
            $viewsFileExtensions = $this->fileExtensions;
            $viewsDirectories = modules()->getDirs( 'Views' );

            if ( ( $theme = presenter()->getItem( 'theme' ) ) instanceof Theme ) {
                $moduleReplacementPath = $theme->getPathName()
                    . DIRECTORY_SEPARATOR
                    . 'modules'
                    . DIRECTORY_SEPARATOR
                    . dash(
                        modules()->current()->getDirName()
                    )
                    . DIRECTORY_SEPARATOR;

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

    public function render( $return = false )
    {
        parser()->loadVars( presenter()->getArrayCopy() );

        if( presenter()->title instanceof Title ) {
            $this->document->title->text( presenter()->title->browser->__toString() );
        }

        if ( ( $theme = presenter()->getVariable( 'theme' ) ) instanceof Theme ) {
            $themeLayout = $theme->getLayout()->getRealPath();

            // Import body tag attributes
            if ( preg_match( '#<body(.*?)>#is', file_get_contents( $themeLayout ), $matches ) ) {
                $bodyXml = simplexml_load_string( str_replace( '>', '/>', $matches[ 0 ] ) );

                foreach ( $bodyXml->attributes() as $name => $value ) {
                    $this->document->body->setAttribute( $name, $value );
                }
            }

            $this->document->body->setAttribute( 'data-module', modules()->current()->getParameter() );
            $this->document->body->setAttribute( 'data-controller', router()->getController()->getParameter() );

            parser()->loadFile( $themeLayout );
            $this->document->loadHTML( parser()->parse() );
        } else {
            $this->document->find( 'body' )->append( presenter()->partials->__get( 'content' ) );
        }

        if ( input()->env( 'DEBUG_STAGE' ) === 'DEVELOPER' and presenter()->debugToolBar === true ) {
            $this->document->find( 'body' )->append( ( new Toolbar() )->__toString() );
        }

        if ( $return === true ) {
            return $this->document->saveHTML();
        }

        output()->send( $this->document->saveHTML() );
    }
}