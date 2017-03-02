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

class Router
{
    protected $string;

    protected $segments = [];

    protected $options  = [];

    protected $commander;

    public function parseRequest ()
    {
        $argv = $_SERVER[ 'argv' ];

        if ( $_SERVER[ 'SCRIPT_NAME' ] === $_SERVER[ 'argv' ][ 0 ] ) {
            array_shift( $argv );
        }

        $this->string = str_replace( [ '/', '\\' ], '/', $argv[ 0 ] );
        $this->segments = explode( '/', $this->string );

        $options = array_slice( $argv, 1 );

        foreach ( $options as $option ) {
            if ( strpos( $option, '--' ) !== false
                 || strpos( $option, '-' ) !== false
            ) {
                $option = str_replace( [ '-', '--' ], '', $option );
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
                    $this->options[ $option ] = $value;
                }
            }
        }

        $this->parseSegments( $this->segments );
    }

    final private function parseSegments ( array $segments )
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

    public function getCommander ()
    {
        return $this->commander;
    }

    final protected function setCommander ( Router\Registries\Commander $commander, array $uriSegments = [] )
    {
        if ( is_null( $commander->name ) ) {
            output()->sendError( 400 );
        }

        // Add Commander PSR4 Namespace
        loader()->addNamespace( $commander->getNamespaceName(), $commander->getFileInfo()->getPath() );

        $commanderMethod = camelcase( reset( $uriSegments ) );
        $commanderMethodParams = array_slice( $uriSegments, 1 );

        if ( null !== $commander->getRequestMethod() ) {
            $commander->setRequestMethodArgs( $commanderMethodParams );
        } elseif ( count( $uriSegments ) ) {
            if ( $commander->hasMethod( 'reroute' ) ) {
                $commander
                    ->setRequestMethod( 'reroute' )
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
            } elseif ( $commander->hasMethod( 'index' ) ) {
                $index = $commander->getMethod( 'index' );

                if ( $index->getNumberOfParameters() > 0 ) {

                    array_unshift( $commanderMethodParams, $commanderMethod );

                    $commander
                        ->setRequestMethod( 'index' )
                        ->setRequestMethodArgs( $commanderMethodParams );
                } else {
                    output()->sendError( 404 );
                }
            }
        } elseif ( $commander->hasMethod( 'reroute' ) ) {
            $commander
                ->setRequestMethod( 'reroute' )
                ->setRequestMethodArgs( [ 'index', [] ] );
        } elseif ( $commander->hasMethod( 'index' ) ) {
            $commander
                ->setRequestMethod( 'index' );
        }

        // Set Router Commander
        $this->commander = $commander;
    }

    // ------------------------------------------------------------------------

    final private function validateSegmentsCommander ( array $segments )
    {
        $numSegments = count( $segments );
        $commanderRegistry = null;
        $uriSegments = [];
        $commandersDirectories = modules()->getDirs( 'Commanders' );

        print_out($commandersDirectories);

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            $commanderFilename = implode( DIRECTORY_SEPARATOR, $routedSegments );
            $commanderFilename = prepare_filename( $commanderFilename ) . '.php';

            if ( empty( $commanderRegistry ) ) {
                foreach ( $commandersDirectories as $commanderDirectory ) {
                    if ( is_file( $commanderFilePath = $commanderDirectory . $commanderFilename ) ) {
                        $uriSegments = array_diff( $segments, $routedSegments );
                        $commanderRegistry = new Router\Registries\Commander( $commanderFilePath );
                        break;
                    }
                }
            } elseif ( $commanderRegistry instanceof Router\Registries\Commander ) {
                $this->setCommander( $commanderRegistry, $uriSegments );

                return true;
                break;
            }
        }

        return false;
    }
}