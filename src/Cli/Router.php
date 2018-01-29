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

namespace O2System\Framework\Cli;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Router\Datastructures\Commander;

/**
 * Class Router
 *
 * @package O2System\Framework\Cli
 */
class Router
{
    /**
     * Router::$string
     *
     * Router request string.
     *
     * @var string
     */
    protected $string;

    /**
     * Router::$segments
     *
     * Router request segments.
     *
     * @var array
     */
    protected $segments = [];

    /**
     * Router::$commander
     *
     * Router request commander.
     *
     * @var Commander
     */
    protected $commander;

    // -----------------------------------------------------------------------

    /**
     * Router::parseRequest
     *
     * Parse server argv to determine requested commander.
     *
     * @return void
     */
    public function parseRequest()
    {
        $argv = $_SERVER[ 'argv' ];

        if ( $_SERVER[ 'SCRIPT_NAME' ] === $_SERVER[ 'argv' ][ 0 ] ) {
            array_shift( $argv );

            if ( empty( $argv ) ) {
                return;
            }
        }

        $this->string = str_replace( [ '/', '\\', ':' ], '/', $argv[ 0 ] );
        $this->segments = explode( '/', $this->string );

        if ( strpos( $this->segments[ 0 ], '--' ) !== false
            || strpos( $this->segments[ 0 ], '-' ) !== false
        ) {
            $options = $this->segments;
            $this->segments = [];
        } else {
            $options = array_slice( $argv, 1 );
        }

        foreach ( $options as $option ) {
            if ( strpos( $option, '--' ) !== false
                || strpos( $option, '-' ) !== false
            ) {
                $option = str_replace( [ '-', '--' ], '', $option );
                $option = str_replace( ':', '=', $option );
                $value = null;

                if ( strpos( $option, '=' ) !== false ) {
                    $optionParts = explode( '=', $option );
                    $option = $optionParts[ 0 ];
                    $value = $optionParts[ 1 ];
                } else {
                    $value = current( $options );
                }

                if ( $value === 'true' ) {
                    $value = true;
                } elseif ( $value === 'false' ) {
                    $value = false;
                }

                if ( strpos( $value, '--' ) === false
                    || strpos( $value, '-' ) === false
                ) {
                    $_GET[ $option ] = $value;
                } else {
                    $_GET[ $option ] = null;
                }
            }
        }

        if ( array_key_exists( 'verbose', $_GET ) or array_key_exists( 'v', $_GET ) ) {
            $_ENV[ 'VERBOSE' ] = true;
        }

        $this->parseSegments( $this->segments );
    }

    // ------------------------------------------------------------------------

    /**
     * Router::parseSegments
     *
     * Parse and validate requested segments.
     *
     * @param array $segments
     */
    final private function parseSegments( array $segments )
    {
        static $reflection;

        if ( empty( $reflection ) ) {
            $reflection = new \ReflectionClass( $this );
        }

        foreach ( $reflection->getMethods() as $method ) {
            if ( strpos( $method->name, 'validateSegments' ) !== false ) {
                if ( $this->{$method->name}( $segments ) ) {
                    break;
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::getCommander
     *
     * Gets requested commander.
     *
     * @return \O2System\Framework\Cli\Router\Datastructures\Commander
     */
    public function getCommander()
    {
        return $this->commander;
    }

    // ------------------------------------------------------------------------

    /**
     * Router::setCommander
     *
     * Sets requested commander.
     *
     * @param \O2System\Framework\Cli\Router\Datastructures\Commander $commander
     * @param array                                               $uriSegments
     */
    final protected function setCommander( Router\Datastructures\Commander $commander, array $uriSegments = [] )
    {
        // Add Commander PSR4 Namespace
        loader()->addNamespace( $commander->getNamespaceName(), $commander->getFileInfo()->getPath() );

        $commanderMethod = camelcase( reset( $uriSegments ) );
        $commanderMethodParams = array_slice( $uriSegments, 1 );

        if ( null !== $commander->getRequestMethod() ) {
            $commander->setRequestMethodArgs( $commanderMethodParams );
        } elseif ( count( $uriSegments ) ) {
            if ( $commander->hasMethod( 'route' ) ) {
                $commander
                    ->setRequestMethod( 'route' )
                    ->setRequestMethodArgs(
                        [
                            $commanderMethod,
                            $commanderMethodParams,
                        ]
                    );
            } elseif ( $commander->hasMethod( $commanderMethod ) ) {
                $method = $commander->getMethod( $commanderMethod );

                if ( $method->isPublic() ) {
                    $commander
                        ->setRequestMethod( $commanderMethod )
                        ->setRequestMethodArgs( $commanderMethodParams );
                } elseif ( is_ajax() AND $method->isProtected() ) {
                    $commander
                        ->setRequestMethod( $commanderMethod )
                        ->setRequestMethodArgs( $commanderMethodParams );
                }
            } elseif ( $commander->hasMethod( 'execute' ) ) {
                $execute = $commander->getMethod( 'execute' );

                if ( $execute->getNumberOfParameters() > 0 ) {

                    array_unshift( $commanderMethodParams, $commanderMethod );

                    $commander
                        ->setRequestMethod( 'execute' )
                        ->setRequestMethodArgs( $commanderMethodParams );
                } else {
                    output()->sendError( 404 );
                }
            }
        } elseif ( $commander->hasMethod( 'route' ) ) {
            $commander
                ->setRequestMethod( 'route' )
                ->setRequestMethodArgs( [ 'execute', [] ] );
        } elseif ( $commander->hasMethod( 'execute' ) ) {
            $commander
                ->setRequestMethod( 'execute' );
        }

        // Set Router Commander
        $this->commander = $commander;
    }

    // ------------------------------------------------------------------------

    /**
     * Router::validateSegmentsCommander
     *
     * @param array $segments
     *
     * @return bool
     */
    final private function validateSegmentsCommander( array $segments )
    {
        $numSegments = count( $segments );
        $commanderRegistry = null;
        $uriSegments = [];
        $commandersDirectories = modules()->getDirs( 'Commanders' );

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            $commanderFilename = implode( DIRECTORY_SEPARATOR, $routedSegments );
            $commanderFilename = prepare_filename( $commanderFilename ) . '.php';

            foreach ( $commandersDirectories as $commanderDirectory ) {
                if ( is_file( $commanderFilePath = $commanderDirectory . $commanderFilename ) ) {
                    $uriSegments = array_diff( $segments, $routedSegments );
                    $commanderRegistry = new Router\Datastructures\Commander( $commanderFilePath );
                    break;
                }
            }

            if ( $commanderRegistry instanceof Router\Datastructures\Commander ) {
                $this->setCommander($commanderRegistry, $uriSegments);
                break;
                return true;
            }
        }

        return false;
    }
}