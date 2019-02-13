<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Containers\Modules\DataStructures;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Module
 *
 * @package O2System\Framework\Containers\Modules\DataStructures
 */
class Module extends SplDirectoryInfo
{
    /**
     * Module::$type
     *
     * @var string
     */
    protected $type = 'MODULE';

    /**
     * Module::$namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Module::$segments
     *
     * @var string
     */
    protected $segments;

    /**
     * Module::$parentSegments
     *
     * @var string
     */
    protected $parentSegments;

    /**
     * Module::$properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Module::$presets
     *
     * @var array
     */
    protected $presets = [];

    // ------------------------------------------------------------------------

    /**
     * Module::__construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent::__construct($dir);

        $this->namespace = prepare_namespace(str_replace(PATH_ROOT, '', $dir), false);
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getSegments
     *
     * @param bool $returnArray
     *
     * @return array|string
     */
    public function getSegments($returnArray = true)
    {
        if ($returnArray) {
            return explode('/', $this->segments);
        }

        return $this->segments;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setSegments
     *
     * @param array|string $segments
     *
     * @return static
     */
    public function setSegments($segments)
    {
        $this->segments = is_array($segments) ? implode('/', $segments) : $segments;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getParentSegments
     *
     * @param bool $returnArray
     *
     * @return array|string
     */
    public function getParentSegments($returnArray = true)
    {
        if ($returnArray) {
            return explode('/', $this->parentSegments);
        }

        return $this->parentSegments;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setParentSegments
     *
     * @param array|string $parentSegments
     *
     * @return static
     */
    public function setParentSegments($parentSegments)
    {
        $this->parentSegments = is_array($parentSegments) ? implode('/', $parentSegments) : $parentSegments;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getCode
     *
     * @return string
     */
    public function getCode()
    {
        return strtoupper(substr(md5(spl_object_hash($this)), 2, 7));
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getChecksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getProperties
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setProperties
     *
     * @param array $properties
     *
     * @return static
     */
    public function setProperties(array $properties)
    {
        if (isset($properties[ 'presets' ])) {
            $this->setPresets($properties[ 'presets' ]);

            unset($properties[ 'presets' ]);
        }

        $this->properties = $properties;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getPresets
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getPresets()
    {
        return new SplArrayObject($this->presets);
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setPresets
     *
     * @param array $presets
     *
     * @return static
     */
    public function setPresets(array $presets)
    {
        $this->presets = $presets;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getDefaultControllerClassName
     *
     * @return string
     */
    public function getDefaultControllerClassName()
    {
        return $this->getNamespace() . 'Controllers\\' . $this->getDirName();
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getNamespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setNamespace
     *
     * @param string $namespace
     *
     * @return static
     */
    public function setNamespace($namespace)
    {
        $this->namespace = trim($namespace, '\\') . '\\';

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getThemes
     *
     * @return array
     */
    public function getThemes()
    {
        $directory = new SplDirectoryInfo($this->getResourcesDir() . 'themes' . DIRECTORY_SEPARATOR);

        $themes = [];
        foreach ($directory->getTree() as $themeName => $themeTree) {
            if (($theme = $this->getTheme($themeName)) instanceof Module\Theme) {
                $themes[ $themeName ] = $theme;
            }
        }

        return $themes;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getResourcesDir
     *
     * @return string
     */
    public function getResourcesDir()
    {
        if ($this->getParameter() === DIR_APP) {
            return PATH_RESOURCES;
        }

        return PATH_RESOURCES . $this->getParameter() . DIRECTORY_SEPARATOR;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getTheme
     *
     * @param string $theme
     * @param bool   $failover
     *
     * @return bool|Module\Theme
     */
    public function getTheme($theme, $failover = true)
    {
        $theme = dash($theme);

        if ($failover === false) {
            if (is_dir($themePath = $this->getResourcesDir() . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR)) {
                $themeObject = new Module\Theme($themePath);

                if ($themeObject->isValid()) {
                    return $themeObject;
                }
            }
        } else {
            foreach (modules() as $module) {
                if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                    continue;
                } elseif ($themeObject = $module->getTheme($theme, false)) {
                    return $themeObject;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getPublicDir
     *
     * @return string
     */
    public function getPublicDir()
    {
        return PATH_PUBLIC . strtolower(str_replace(PATH_APP, '', $this->getRealPath()));
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getType
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::setType
     *
     * @param string $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = strtoupper($type);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getDir
     *
     * @param string $dirName
     * @param bool   $psrDir
     *
     * @return bool|string
     */
    public function getDir($dirName, $psrDir = false)
    {
        $dirName = $psrDir === true ? prepare_class_name($dirName) : $dirName;
        $dirName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dirName);

        if (is_dir($dirPath = $this->getRealPath() . $dirName)) {
            return $dirPath . DIRECTORY_SEPARATOR;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::hasTheme
     *
     * @param string $theme
     *
     * @return bool
     */
    public function hasTheme($theme)
    {
        if (is_dir($this->getThemesPath() . $theme)) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Module::getThemesPath
     *
     * @return string
     */
    public function getThemesPath()
    {
        if ($this->getParameter() === DIR_APP) {
            return PATH_RESOURCES . 'themes' . DIRECTORY_SEPARATOR;
        }

        return PATH_RESOURCES . $this->getParameter() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR;

    }

    // ------------------------------------------------------------------------

    /**
     * Module::getParameter
     *
     * @return string
     */
    public function getParameter()
    {
        return snakecase($this->getDirName());
    }

    // ------------------------------------------------------------------------

    /**
     * Module::loadModel
     */
    public function loadModel()
    {
        $modelClassName = $this->namespace . 'Models\Base';

        if (class_exists($modelClassName)) {
            models()->load($modelClassName, strtolower($this->type));
        }
    }
}