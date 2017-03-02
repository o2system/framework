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

use O2System\Framework\Http\Message\Uri;
use O2System\Kernel\Registries\Config;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class Assets
 *
 * @package O2System\Framework\Http\Presenter
 */
class Assets
{
    use FilePathCollectorTrait;

    public  $isCombine  = false;

    private $iconAssets = [];

    private $fontAssets = [];

    private $cssAssets  = [];

    private $jsAssets   = [];

    /**
     * Assets::__construct
     */
    public function __construct ()
    {
        $this->setFileDirName( 'assets' );
        $this->addFilePath( PATH_PUBLIC );
    }

    public function loadFiles ( $assets )
    {
        if ( $assets instanceof Config ) {
            $assets = $assets->getArrayCopy();
        }

        foreach ( $assets as $type => $item ) {
            $addMethod = 'add' . ucfirst( $type );

            if ( method_exists( $this, $addMethod ) ) {
                call_user_func_array( [ &$this, $addMethod ], [ $item ] );
            }
        }
    }

    public function addJs ( $js )
    {
        $js = is_string( $js ) ? [ $js ] : $js;

        foreach ( $js as $filename ) {
            if ( strpos( $filename, '//' ) !== false ) {
                $this->jsAssets[] = $filename;
            } elseif ( is_file( $filename ) ) {
                $this->jsAssets[] = path_to_url( $filename );
            } else {
                foreach ( $this->filePaths as $filePath ) {
                    $filePath .= 'js' . DIRECTORY_SEPARATOR;
                    if ( is_file( $filePath . $filename . '.min.js' ) ) {
                        $this->jsAssets[] = $this->getUrl( $filePath . $filename . '.min.js' );
                        break;
                    } elseif ( is_file( $filePath . $filename . '.js' ) ) {
                        $this->jsAssets[] = $this->getUrl( $filePath . $filename . '.js' );
                        break;
                    }
                }
            }
        }
    }

    public function getUrl ( $realPath )
    {
        return ( new Uri() )
            ->withQuery( null )
            ->withSegments(
                new Uri\Segments(
                    str_replace(
                        [ PATH_PUBLIC, DIRECTORY_SEPARATOR ],
                        [ '', '/' ],
                        $realPath
                    )
                )
            )
            ->__toString();
    }

    public function addCss ( $css )
    {
        $css = is_string( $css ) ? [ $css ] : $css;

        foreach ( $css as $filename ) {
            if ( strpos( $filename, '//' ) !== false ) {
                $this->cssAssets[] = $filename;
            } elseif ( is_file( $filename ) ) {
                $this->cssAssets[] = path_to_url( $filename );
            } else {
                foreach ( $this->filePaths as $filePath ) {
                    $filePath .= 'css' . DIRECTORY_SEPARATOR;
                    if ( is_file( $filePath . $filename . '.min.css' ) ) {
                        $this->cssAssets[] = $this->getUrl( $filePath . $filename . '.min.css' );
                        break;
                    } elseif ( is_file( $filePath . $filename . '.css' ) ) {
                        $this->cssAssets[] = $this->getUrl( $filePath . $filename . '.css' );
                        break;
                    }
                }
            }
        }
    }

    public function addIcon ( $icon )
    {
        $this->addIcons( $icon );
    }

    public function addIcons ( $icons )
    {
        $icons = is_string( $icons ) ? [ $icons ] : $icons;

        foreach ( $icons as $filename ) {
            if ( strpos( $filename, '//' ) !== false ) {
                $this->iconAssets[] = $filename;
            } elseif ( is_file( $filename ) ) {
                $this->iconAssets[] = path_to_url( $filename );
            } else {
                foreach ( $this->filePaths as $filePath ) {
                    $filePath .= 'icons' . DIRECTORY_SEPARATOR;
                    if ( is_file( $filePath . $filename ) ) {
                        $this->iconAssets[] = ( new Uri() )
                            ->withQuery( null )
                            ->addPath(
                                str_replace( [ PATH_PUBLIC, DIRECTORY_SEPARATOR ], [ '', '/' ], $filePath . $filename )
                            )
                            ->__toString();
                        break;
                    }
                }
            }
        }
    }

    public function addFont ( $font )
    {
        $this->addFonts( $font );
    }

    public function addFonts ( $fonts )
    {
        $fonts = is_string( $fonts ) ? [ $fonts ] : $fonts;

        foreach ( $fonts as $filename ) {
            if ( strpos( $filename, '//' ) !== false ) {
                $this->fontAssets[] = $filename;
            } elseif ( is_file( $filename ) ) {
                $this->fontAssets[] = path_to_url( $filename );
            } else {
                foreach ( $this->filePaths as $filePath ) {
                    $filePath .= 'fonts' . DIRECTORY_SEPARATOR . $filename . DIRECTORY_SEPARATOR;
                    if ( is_file( $filePath . $filename . '.min.css' ) ) {
                        $this->fontAssets[] = $this->getUrl( $filePath . $filename . '.min.css' );
                        break;
                    } elseif ( is_file( $filePath . $filename . '.css' ) ) {
                        $this->fontAssets[] = $this->getUrl( $filePath . $filename . '.css' );
                        break;
                    }
                }
            }
        }
    }

    public function addPackage ( $package )
    {
        $this->addPackages( $package );
    }

    public function addPackages ( $packages )
    {
        $packages = is_string( $packages ) ? [ $packages ] : $packages;

        foreach ( $packages as $offset => $package ) {
            if ( is_string( $package ) ) {
                foreach ( $this->filePaths as $filePath ) {
                    $filePath .= 'packages' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR;

                    // Add CSS
                    if ( is_file( $filePath . $package . '.min.css' ) ) {
                        $this->cssAssets[ $package ] = $this->getUrl( $filePath . $package . '.min.css' );
                    } elseif ( is_file( $filePath . $package . '.css' ) ) {
                        $this->cssAssets[ $package ] = $this->getUrl( $filePath . $package . '.css' );
                    }

                    // Add JS
                    if ( is_file( $filePath . $package . '.min.js' ) ) {
                        $this->jsAssets[ $package ] = $this->getUrl( $filePath . $package . '.min.js' );
                    } elseif ( is_file( $filePath . $package . '.js' ) ) {
                        $this->jsAssets[ $package ] = $this->getUrl( $filePath . $package . '.js' );
                    }
                }
            } else {
                if ( $package instanceof Config ) {
                    $package = $package->getArrayCopy();
                }

                foreach ( $this->filePaths as $filePath ) {
                    $packageName = $offset;
                    $packagePath = $filePath . 'packages' . DIRECTORY_SEPARATOR . $packageName . DIRECTORY_SEPARATOR;

                    if ( is_dir( $packagePath ) ) {

                        // Add Css
                        if ( is_file( $packagePath . $packageName . '.min.css' ) ) {
                            $this->cssAssets[ $packageName ] = $this->getUrl(
                                $packagePath . $packageName . '.min.css'
                            );
                        } elseif ( is_file( $packagePath . $packageName . '.css' ) ) {
                            $this->cssAssets[ $packageName ] = $this->getUrl( $packagePath . $packageName . '.css' );
                        }

                        // Add Js
                        if ( is_file( $packagePath . $packageName . '.min.js' ) ) {
                            $this->jsAssets[ $packageName ] = $this->getUrl( $packagePath . $packageName . '.min.js' );
                        } elseif ( is_file( $packagePath . $packageName . '.js' ) ) {
                            $this->jsAssets[ $packageName ] = $this->getUrl( $packagePath . $packageName . '.js' );
                        }

                        // Add Package Theme
                        if ( isset( $package[ 'themes' ] ) ) {
                            $themeName = $package[ 'themes' ];
                            $themeFilePath = $packagePath . 'themes' . DIRECTORY_SEPARATOR . $themeName . DIRECTORY_SEPARATOR;
                            if ( is_file( $themeFilePath . $themeName . '.min.css' ) ) {
                                $this->cssAssets[ $themeName ] = $this->getUrl(
                                    $themeFilePath . $themeName . '.min.css'
                                );
                            } elseif ( is_file( $themeFilePath . $themeName . '.css' ) ) {
                                $this->cssAssets[ $themeName ] = $this->getUrl( $themeFilePath . $themeName . '.css' );
                            }
                        }

                        // Add Package Plugins
                        if ( isset( $package[ 'plugins' ] ) ) {
                            foreach ( $package[ 'plugins' ] as $plugin ) {
                                $pluginFilePath = $packagePath . 'plugins' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR;

                                // Add Package Plugin Css
                                if ( is_file( $pluginFilePath . $plugin . '.min.css' ) ) {
                                    $this->cssAssets[ $plugin ] = $this->getUrl(
                                        $pluginFilePath . $plugin . '.min.css'
                                    );
                                } elseif ( is_file( $pluginFilePath . $plugin . '.css' ) ) {
                                    $this->cssAssets[ $plugin ] = $this->getUrl( $pluginFilePath . $plugin . '.css' );
                                }

                                // Add Package Plugin Js
                                if ( is_file( $pluginFilePath . $plugin . '.min.js' ) ) {
                                    $this->jsAssets[ $plugin ] = $this->getUrl( $pluginFilePath . $plugin . '.min.js' );
                                } elseif ( is_file( $pluginFilePath . $plugin . '.js' ) ) {
                                    $this->jsAssets[ $plugin ] = $this->getUrl( $pluginFilePath . $plugin . '.js' );
                                }
                            }
                        }

                        break;
                    }
                }
            }
        }
    }

    public function __get ( $position )
    {
        switch ( $position ) {
            case 'header':

                return $this->getHeaderOutput();

                break;
            case 'footer':

                return $this->getFooterOutput();

                break;
        }
    }

    private function getHeaderOutput ()
    {
        $headerOutput = [];

        foreach ( $this->fontAssets as $fontAsset ) {
            $headerOutput[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$fontAsset\">";
        }

        foreach ( $this->cssAssets as $cssAsset ) {
            $headerOutput[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$cssAsset\">";
        }

        return implode( PHP_EOL, $headerOutput );
    }

    private function getFooterOutput ()
    {
        $footerOutput = [];

        foreach ( $this->jsAssets as $jsAsset ) {
            $footerOutput[] = "<script type=\"text/javascript\" src=\"$jsAsset\"></script>";
        }

        return implode( PHP_EOL, $footerOutput );
    }

    public function image ( $image )
    {
        $image = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $image );

        foreach ( $this->filePaths as $filePath ) {
            $filePath .= 'images' . DIRECTORY_SEPARATOR;

            if ( is_file( $filePath . $image ) ) {
                return $this->getUrl( $filePath . $image );
                break;
            }
        }
    }
}