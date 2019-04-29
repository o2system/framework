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

namespace O2System\Framework\Containers\Modules\DataStructures\Module;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Widget
 *
 * @package O2System\Framework\Containers\Modules\DataStructures\Module
 */
class Widget extends SplDirectoryInfo
{
    /**
     * Widget::$properties
     *
     * @var array
     */
    private $properties = [];

    /**
     * Widget::$presets
     *
     * @var array
     */
    private $presets = [];

    /**
     * Widget::__construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent::__construct($dir);

        // Set Widget Properties
        if (is_file($propFilePath = $dir . 'widget.json')) {
            $properties = json_decode(file_get_contents($propFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($properties[ 'config' ])) {
                    $this->presets = $properties[ 'presets' ];
                    unset($properties[ 'presets' ]);
                }

                $this->properties = $properties;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::isValid
     *
     * @return bool
     */
    public function isValid()
    {
        if (count($this->properties)) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getParameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->getDirName();
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getCode
     *
     * @return string
     */
    public function getCode()
    {
        return strtoupper(substr(md5($this->getDirName()), 2, 7));
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getChecksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getProperties
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getNamespace
     *
     * @return mixed|string
     */
    public function getNamespace()
    {
        if (isset($this->properties[ 'namespace' ])) {
            return $this->properties[ 'namespace' ];
        }

        $dir = $this->getRealPath();
        $dirParts = explode('Widgets' . DIRECTORY_SEPARATOR, $dir);
        $namespace = loader()->getDirNamespace(reset($dirParts)) . 'Widgets\\' . str_replace(['/', DIRECTORY_SEPARATOR],
                '\\', end($dirParts));

        return $namespace;
    }

    // ------------------------------------------------------------------------

    /**
     * Widget::getConfig
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getPresets()
    {
        return new SplArrayObject($this->presets);
    }
}