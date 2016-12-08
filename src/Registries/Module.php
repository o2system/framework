<?php
/**
 * v6.0.0-svn
 *
 * @author      Steeve Andrian Salim
 * @created     14/11/2016 21:11
 * @copyright   Copyright (c) 2016 Steeve Andrian Salim
 */

namespace O2System\Framework\Registries;


use O2System\Framework\Registries\Module\Theme;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

class Module extends SplDirectoryInfo
{
    private $type = 'MODULE';

    /**
     * Module Namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Module Segments
     *
     * @var string
     */
    private $segments;

    /**
     * Module Parent Segments
     *
     * @var string
     */
    private $parentSegments;

    /**
     * Module Properties
     *
     * @var array
     */
    private $properties = [ ];

    /**
     * Module Config
     *
     * @var array
     */
    private $config = [ ];

    public function __construct ( $dir )
    {
        parent::__construct( $dir );

        $this->namespace = prepare_namespace( str_replace( PATH_ROOT, '', $dir ) );
    }

    public function getType ()
    {
        return $this->type;
    }

    public function setType ( $type )
    {
        $this->type = strtoupper( $type );

        return $this;
    }

    public function setSegments ( $segments )
    {
        $this->segments = is_array( $segments ) ? implode( '/', $segments ) : $segments;

        return $this;
    }

    public function setParentSegments ( $parentSegments )
    {
        $this->parentSegments = is_array( $parentSegments ) ? implode( '/', $parentSegments ) : $parentSegments;

        return $this;
    }

    public function getParameter ()
    {
        return strtolower( $this->getDirName() );
    }

    public function getCode ()
    {
        return strtoupper( substr( md5( $this->getDirName() ), 2, 7 ) );
    }

    public function getChecksum ()
    {
        return md5( $this->getMTime() );
    }

    public function getProperties ()
    {
        return new SplArrayObject( $this->properties );
    }

    public function setProperties ( array $properties )
    {
        $this->properties = $properties;

        return $this;
    }

    public function getConfig ()
    {
        return new SplArrayObject( $this->config );
    }

    public function setConfig ( array $config )
    {
        $this->config = $config;

        return $this;
    }

    public function getNamespace ()
    {
        return $this->namespace;
    }

    public function setNamespace ( $namespace )
    {
        $this->namespace = trim( $namespace, '\\' ) . '\\';

        return $this;
    }

    public function getTheme ( $theme, $failover = true )
    {
        $theme = dash( $theme );

        if ( $failover === false ) {
            if ( is_dir( $themePath = $this->getThemesPath() . $theme . DIRECTORY_SEPARATOR ) ) {
                $themeObject = new Theme( $themePath );

                if ( $themeObject->isValid() ) {
                    return $themeObject;
                }
            }
        } else {
            foreach ( modules() as $module ) {
                if ( in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {
                    continue;
                } elseif ( $themeObject = $module->getTheme( $theme, false ) ) {
                    return $themeObject;
                }
            }
        }

        return false;
    }

    public function getThemesPath ()
    {
        return str_replace( PATH_APP, PATH_PUBLIC, $this->getRealPath() ) . 'themes' . DIRECTORY_SEPARATOR;
    }

    public function hasTheme ( $theme )
    {
        if ( is_dir( $this->getThemesPath() . $theme ) ) {
            return true;
        }

        return false;
    }

    public function loadModel ()
    {
        $modelClassName = $this->namespace . 'Base\\Model';

        if ( class_exists( $modelClassName ) ) {
            models()->register( strtolower( $this->type ), new $modelClassName() );
        }
    }
}