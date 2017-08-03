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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

/**
 * Class Assets
 *
 * @package O2System\Framework\Http\Presenter
 */
class Assets
{
    protected $head;
    protected $body;

    /**
     * Assets::__construct
     */
    public function __construct()
    {
        loader()->addPublicDir( 'assets' );

        $this->head = new Assets\Positions\Head();
        $this->body = new Assets\Positions\Body();
    }

    public function __get( $property )
    {
        return isset( $this->{$property} ) ? $this->{$property} : null;
    }

    public function autoload( array $assets )
    {
        foreach ( $assets as $position => $collections ) {
            if ( property_exists( $this, $position ) ) {

                if ( $collections instanceof \ArrayObject ) {
                    $collections = $collections->getArrayCopy();
                }

                $this->{$position}->loadCollections( $collections );
            } elseif ( $position === 'packages' ) {
                $this->loadPackages( $collections );
            } elseif ( $position === 'css' ) {
                $this->loadCss( $collections );
            } elseif ( $position === 'js' ) {
                $this->loadJs( $collections );
            }
        }
    }

    public function loadPackages( $packages )
    {
        foreach ( $packages as $package => $files ) {
            if ( is_string( $files ) ) {
                $this->loadPackage( $files );
            } elseif ( is_array( $files ) ) {
                $this->loadPackage( $package, $files );
            }
        }
    }

    public function loadPackage( $package, $subPackages = [] )
    {
        $packageDir = implode( DIRECTORY_SEPARATOR, [
                'packages',
                $package,
            ] ) . DIRECTORY_SEPARATOR;

        if ( count( $subPackages ) ) {

            if ( array_key_exists( 'libraries', $subPackages ) ) {
                foreach ( $subPackages[ 'libraries' ] as $subPackageFile ) {
                    $pluginDir = $packageDir . 'libraries' . DIRECTORY_SEPARATOR;
                    $pluginName = $subPackageFile;

                    if ( $this->body->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.js' ) ) {
                        $this->head->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.css' );
                    } else {
                        $this->body->loadFile( $pluginDir . $pluginName . '.js' );
                    }
                }

                unset( $subPackages[ 'libraries' ] );
            }

            $this->head->loadFile( $packageDir . $package . '.css' );
            $this->body->loadFile( $packageDir . $package . '.js' );

            foreach ( $subPackages as $subPackage => $subPackageFiles ) {
                if ( $subPackage === 'theme' or $subPackage === 'themes' ) {
                    $themeDir = $packageDir . 'themes' . DIRECTORY_SEPARATOR;
                    $themeName = $subPackageFiles;

                    if ( $this->head->loadFile( $themeDir . $themeName . DIRECTORY_SEPARATOR . $themeName . '.css' ) ) {
                        $this->body->loadFile( $themeDir . $themeName . DIRECTORY_SEPARATOR . $themeName . '.js' );
                    } else {
                        $this->head->loadFile( $themeDir . $themeName . '.css' );
                    }
                } elseif ( $subPackage === 'plugins' ) {
                    foreach ( $subPackageFiles as $subPackageFile ) {
                        $pluginDir = $packageDir . 'plugins' . DIRECTORY_SEPARATOR;
                        $pluginName = $subPackageFile;

                        if ( $this->body->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.js' ) ) {
                            $this->head->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.css' );
                        } else {
                            $this->body->loadFile( $pluginDir . $pluginName . '.js' );
                        }
                    }
                } elseif ( $subPackage === 'libraries' ) {
                    foreach ( $subPackageFiles as $subPackageFile ) {
                        $pluginDir = $packageDir . 'libraries' . DIRECTORY_SEPARATOR;
                        $pluginName = $subPackageFile;

                        if ( $this->body->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.js' ) ) {
                            $this->head->loadFile( $pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.css' );
                        } else {
                            $this->body->loadFile( $pluginDir . $pluginName . '.js' );
                        }
                    }
                }
            }
        } else {
            $this->head->loadFile( $packageDir . $package . '.css' );
            $this->body->loadFile( $packageDir . $package . '.js' );
        }
    }

    public function loadFiles( $assets )
    {
        foreach ( $assets as $type => $item ) {
            $addMethod = 'load' . ucfirst( $type );

            if ( method_exists( $this, $addMethod ) ) {
                call_user_func_array( [ &$this, $addMethod ], [ $item ] );
            }
        }
    }

    public function unloadFiles( $assets )
    {
        if ( $assets instanceof Config ) {
            $assets = $assets->getArrayCopy();
        }

        foreach ( $assets as $type => $item ) {

            if ( is_array( $item ) ) {
                foreach ( $item as $filename ) {
                    if ( array_key_exists( $filename, $this->{$type . 'Assets'} ) ) {
                        unset( $this->{$type . 'Assets'}[ $filename ] );
                    }
                }
            } elseif ( is_string( $item ) ) {
                if ( array_key_exists( $item, $this->{$type . 'Assets'} ) ) {
                    unset( $this->{$type . 'Assets'}[ $item ] );
                }
            }
        }
    }

    public function loadJs( $files, $position = 'body' )
    {
        $files = is_string( $files ) ? [ $files ] : $files;
        $this->{$position}->loadCollections( [ 'js' => $files ] );
    }

    public function loadCss( $files )
    {
        $files = is_string( $files ) ? [ $files ] : $files;
        $this->head->loadCollections( [ 'css' => $files ] );
    }

    public function theme( $path )
    {
        $path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $path );

        if ( is_file( $filePath = presenter()->theme->active->getRealPath() . $path ) ) {
            return path_to_url( $filePath );
        }
    }

    public function file( $file ) {
        $file = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $file );

        foreach ( loader()->getPublicDirs( true ) as $filePath ) {
            if ( is_file( $filePath . $file ) ) {
                return path_to_url( $filePath . $file );
                break;
            }
        }
    }

    public function image( $image )
    {
        $image = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $image );

        foreach ( loader()->getPublicDirs( true ) as $filePath ) {
            $filePath .= 'img' . DIRECTORY_SEPARATOR;

            if ( is_file( $filePath . $image ) ) {
                return path_to_url( $filePath . $image );
                break;
            }
        }
    }
}