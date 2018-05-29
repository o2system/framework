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

namespace O2System\Framework\Datastructures\Module;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Widget
 *
 * @package O2System\Framework\Datastructures\Module
 */
class Widget extends SplDirectoryInfo
{
    /**
     * Widget Properties
     *
     * @var array
     */
    private $properties = [];

    /**
     * Widget Config
     *
     * @var array
     */
    private $config = [];

    /**
     * Widget::__construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent::__construct($dir);

        // Set Widget Properties
        if (is_file($propFilePath = $dir . 'widget.jsprop')) {
            $properties = json_decode(file_get_contents($propFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->properties = $properties;
            }
        }

        // Set Widget Config
        if (is_file($propFilePath = $dir . 'widget.jsconf')) {
            $config = json_decode(file_get_contents($propFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->config = $config;
            }
        }
    }

    public function isValid()
    {
        if (count($this->properties)) {
            return true;
        }

        return false;
    }

    public function getParameter()
    {
        return $this->getDirName();
    }

    public function getCode()
    {
        return strtoupper(substr(md5($this->getDirName()), 2, 7));
    }

    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }

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

    public function getConfig()
    {
        return new SplArrayObject($this->config);
    }
}