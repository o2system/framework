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

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Http\Router\Routes;
use O2System\Framework\Registries;
use O2System\Framework\Services\Hooks;
use O2System\Psr\Cache\CacheItemPoolInterface;
use O2System\Spl\Containers\Registries\SplServiceRegistry;
use O2System\Spl\Datastructures\SplArrayStack;
use O2System\Spl\Info\SplNamespaceInfo;

/**
 * Class Modules
 *
 * @package O2System\Kernel
 */
class Modules extends SplArrayStack
{
    private $types    = [
        'apps',
        'modules',
        'components',
        'plugins',
    ];

    private $registry = [];

    public function __construct ()
    {
        parent::__construct(
            [
                ( new Registries\Module( PATH_KERNEL ) )
                    ->setType( 'KERNEL' )
                    ->setNamespace( 'O2System\Kernel\\' ),
                ( new Registries\Module( PATH_FRAMEWORK ) )
                    ->setType( 'FRAMEWORK' )
                    ->setNamespace( 'O2System\Framework\\' ),
                ( new Registries\Module( PATH_APP ) )
                    ->setType( 'APP' )
                    ->setNamespace( 'App' ),
            ]
        );
    }

    public function push ( $module )
    {
        // Register Framework\Services\Loader Namespace
        loader()->addNamespace( $module->getNamespace(), $module->getRealPath() );

        // Autoload Module Helpers
        $this->autoloadHelpers( $module );

        if ( ! in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {

            // Autoload Module Config
            $this->autoloadConfig( $module );

            // Autoload Module Router Maps
            $this->autoloadRoutes( $module );

            // Autoload Module Hooks Closures
            $this->autoloadHooks( $module );

            // Autoload Module Models
            $this->autoloadModels( $module );

            // Autoload Services Models
            $this->autoloadServices( $module );
        }

        parent::push( $module );
    }

    private function autoloadHelpers ( Registries\Module $module )
    {
        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Helpers.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Helpers.php' ) ) {
            include( $filePath );
        }

        if ( isset( $helpers ) AND is_array( $helpers ) ) {
            loader()->loadHelpers( $helpers );
        }
    }

    private function autoloadConfig ( Registries\Module $module )
    {
        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Config.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Config.php' ) ) {
            include( $filePath );
        }

        if ( isset( $config ) AND is_array( $config ) ) {
            // Set default timezone
            if ( isset( $config[ 'datetime' ][ 'timezone' ] ) ) {
                date_default_timezone_set( $config[ 'datetime' ][ 'timezone' ] );
            }

            // Setup Language Ideom and Locale
            if ( isset( $config[ 'language' ] ) ) {
                language()->setDefault( $config[ 'language' ] );
            }

            config()->merge( $config );

            unset( $config );
        }
    }

    private function autoloadRoutes ( Registries\Module $module )
    {
        // Routes is not available on cli
        if ( is_cli() ) {
            return;
        }

        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Routes.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Routes.php' ) ) {
            include( $filePath );
        }

        if ( isset( $routes ) AND $routes instanceof Routes ) {
            config()->addItem( 'routes', $routes );

            unset( $routes );
        }
    }

    private function autoloadHooks ( Registries\Module $module )
    {
        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Hooks.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Hooks.php' ) ) {
            include( $filePath );
        }

        if ( isset( $hooks ) AND is_array( $hooks ) ) {
            foreach ( $hooks as $event => $closures ) {
                if ( $event === Hooks::PRE_SYSTEM ) {
                    // not supported
                    continue;
                }

                if ( is_array( $closures ) ) {
                    foreach ( $closures as $closure ) {
                        hooks()->addClosure( $closure, $event );
                    }
                } elseif ( $closures instanceof \Closure ) {
                    hooks()->addClosure( $closures, $event );
                }
            }

            unset( $hooks );
        }
    }

    private function autoloadModels ( Registries\Module $module )
    {
        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Models.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Models.php' ) ) {
            include( $filePath );
        }

        if ( isset( $models ) AND is_array( $models ) ) {
            foreach ( $models as $offset => $model ) {
                $service = new SplServiceRegistry( $model );

                if ( $service->isSubclassOf( 'O2System\Framework\Abstracts\AbstractModel' ) OR
                     $service->isSubclassOf( 'O2System\Orm\Abstracts\AbstractModel' )
                ) {
                    models()->attach( $offset, $service );
                }
            }

            unset( $models );
        }
    }

    private function autoloadServices ( Registries\Module $module )
    {
        if ( is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Services.php'
        ) ) {
            include( $filePath );
        } elseif ( is_file( $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Services.php' ) ) {
            include( $filePath );
        }

        if ( isset( $services ) AND is_array( $services ) ) {
            foreach ( $services as $offset => $service ) {
                o2system()->addService( new SplServiceRegistry( $service ), $offset );
            }

            unset( $services );
        }
    }

    public function loadRegistry ()
    {
        $cacheItemPool = cache()->getItemPool( 'default' );

        if ( cache()->hasItemPool( 'registry' ) ) {
            $cacheItemPool = cache()->getItemPool( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            if ( $cacheItemPool->hasItem( 'o2modules' ) ) {
                if ( $registry = $cacheItemPool->getItem( 'o2modules' )->get() ) {
                    $this->registry = $registry;
                } else {
                    $this->registry = $this->fetchRegistry();
                    $cacheItemPool->save( new Item( 'o2modules', $this->registry, false ) );
                }
            } else {
                $this->registry = $this->fetchRegistry();
                $cacheItemPool->save( new Item( 'o2modules', $this->registry, false ) );
            }
        } else {
            $this->registry = $this->fetchRegistry();
        }
    }

    public function fetchRegistry ()
    {
        $registries = [];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( PATH_APP ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $propertiesIterator = new \RegexIterator( $directory, '/^.+\.jsprop/i', \RecursiveRegexIterator::GET_MATCH );

        foreach ( $propertiesIterator as $propertiesFiles ) {
            foreach ( $propertiesFiles as $propertiesFile ) {

                // subversion properties file conflict.
                if ( strpos( $propertiesFile, '.svn' ) !== false ) {
                    continue;
                }

                $propertiesFile = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $propertiesFile );
                $propertiesFileInfo = pathinfo( $propertiesFile );
                $propertiesMetadata = json_decode( file_get_contents( $propertiesFile ), true );

                if ( json_last_error(
                     ) !== JSON_ERROR_NONE OR $propertiesFileInfo[ 'filename' ] === 'widget' OR $propertiesFileInfo[ 'filename' ] === 'language'
                ) {
                    continue;
                }

                $moduleSegments = explode(
                    DIRECTORY_SEPARATOR,
                    trim(
                        str_replace(
                            [
                                PATH_FRAMEWORK,
                                PATH_PUBLIC,
                                PATH_APP,
                                $propertiesFileInfo[ 'basename' ],
                            ],
                            '',
                            $propertiesFile
                        ),
                        DIRECTORY_SEPARATOR
                    )
                );

                array_shift( $moduleSegments );

                $moduleSegments = array_map( 'strtolower', $moduleSegments );
                $moduleNamespace = prepare_namespace(
                    str_replace(
                        PATH_ROOT,
                        '',
                        $propertiesFileInfo[ 'dirname' ]
                    ) . DIRECTORY_SEPARATOR . $propertiesFileInfo[ 'filename' ],
                    false
                );

                if ( isset( $propertiesMetadata[ 'namespace' ] ) ) {
                    $moduleNamespace = $propertiesMetadata[ 'namespace' ];
                    unset( $propertiesMetadata[ 'namespace' ] );
                }

                $modulePluralTypes = $this->addType( $propertiesFileInfo[ 'filename' ] );

                $moduleParentSegments = [];
                if ( false !== ( $moduleTypeSegmentKey = array_search( $modulePluralTypes, $moduleSegments ) ) ) {
                    $moduleParentSegments = array_slice( $moduleSegments, 0, $moduleTypeSegmentKey );

                    $moduleParentSegments = array_diff( $moduleParentSegments, $this->types );
                    $moduleSegments = array_diff( $moduleSegments, $this->types );
                }

                $registryKey = implode( '/', $moduleSegments );

                if ( $registryKey === '' ) {
                    if ( $propertiesFileInfo[ 'dirname' ] . DIRECTORY_SEPARATOR !== PATH_APP and $modulePluralTypes === 'apps' ) {
                        $registryKey = 'apps/' . dash(
                                pathinfo( $propertiesFileInfo[ 'dirname' ], PATHINFO_FILENAME )
                            );
                    }
                } else {
                    $registryKey = 'modules/' . $registryKey;
                }

                $registries[ $registryKey ] = ( new Registries\Module(
                    $propertiesFileInfo[ 'dirname' ]
                ) )
                    ->setType( $propertiesFileInfo[ 'filename' ] )
                    ->setNamespace( $moduleNamespace )
                    ->setSegments( $moduleSegments )
                    ->setParentSegments( $moduleParentSegments )
                    ->setProperties( $propertiesMetadata );
            }
        }

        ksort( $registries );

        return $registries;
    }

    public function getRegistry ()
    {
        return $this->registry;
    }

    public function countRegistry ()
    {
        return count( $this->registry );
    }

    public function updateRegistry ()
    {
        $cacheItemPool = cache()->getObject( 'default' );

        if ( cache()->hasItemPool( 'registry' ) ) {
            $cacheItemPool = cache()->getObject( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            $this->registry = $this->fetchRegistry();
            $cacheItemPool->save( new Item( 'o2modules', $this->registry, false ) );
        }
    }

    public function flushRegistry ()
    {
        $cacheItemPool = cache()->getObject( 'default' );

        if ( cache()->hasItemPool( 'registry' ) ) {
            $cacheItemPool = cache()->getObject( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            $cacheItemPool->deleteItem( 'o2modules' );
        }
    }

    public function addType ( $type )
    {
        $pluralTypes = plural( strtolower( $type ) );

        if ( ! in_array( $pluralTypes, $this->types ) ) {
            array_push( $this->types, $pluralTypes );
        }

        return $pluralTypes;
    }

    /**
     * getApp
     *
     * @param $segments
     *
     * @return bool|Registries\Module
     */
    public function getApp ( $segment )
    {
        $segment = 'apps/' . dash( $segment );

        if ( $this->isExists( $segment ) ) {
            return $this->registry[ $segment ];
        }

        return false;
    }

    /**
     * getModule
     *
     * @param $segments
     *
     * @return bool|Registries\Module
     */
    public function getModule ( $segments )
    {
        $segments = 'modules/' . ( is_array( $segments ) ? implode( '/', array_map( 'dash', $segments ) ) : $segments );

        if ( $this->isExists( $segments ) ) {
            return $this->registry[ $segments ];
        }

        return false;
    }

    public function isExists ( $segments )
    {
        $segments = is_array( $segments ) ? implode( '/', $segments ) : $segments;

        return (bool) array_key_exists( $segments, $this->registry );
    }

    /**
     * current
     *
     * @return Registries\Module
     */
    public function current ()
    {
        return parent::current();
    }

    public function getNamespaces ()
    {
        $namespaces = [];

        foreach ( $this as $key => $module ) {
            if ( $module instanceof Registries\Module ) {
                $namespaces[ $key ] = new SplNamespaceInfo( $module->getNamespace(), $module->getRealPath() );
            }
        }

        return $namespaces;
    }

    public function getDirs ( $dirName, $reverse = false )
    {
        $dirs = [];
        $dirName = prepare_class_name( $dirName );

        foreach ( $this as $module ) {
            if ( $module instanceof Registries\Module ) {
                $dir = $module->getRealPath() . str_replace(
                        [ '\\', '/' ],
                        DIRECTORY_SEPARATOR,
                        $dirName
                    ) . DIRECTORY_SEPARATOR;

                if ( is_dir( $dir ) ) {
                    $dirs[] = $dir;
                }
            }
        }

        return $reverse === true ? array_reverse( $dirs ) : $dirs;
    }
}