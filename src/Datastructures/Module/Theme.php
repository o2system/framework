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

use O2System\Framework\Datastructures\Module\Theme\Layout;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Theme
 *
 * @package O2System\Framework\Datastructures\Metadata
 */
class Theme extends SplDirectoryInfo
{
    /**
     * Theme Properties
     *
     * @var array
     */
    private $properties = [];

    /**
     * Theme Config
     *
     * @var array
     */
    private $config = [];

    /**
     * Theme Layout
     *
     * @var SplFileInfo
     */
    private $layout;

    /**
     * Theme::__construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent::__construct($dir);

        // Set Theme Properties
        if (is_file($propFilePath = $dir . 'theme.jsprop')) {
            $properties = json_decode(file_get_contents($propFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->properties = $properties;
            }
        }

        // Set Theme Config
        if (is_file($propFilePath = $dir . 'theme.jsconf')) {
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

    public function getConfig()
    {
        return new SplArrayObject($this->config);
    }

    public function getUrl($path = null)
    {
        return path_to_url($this->getRealPath() . $path);
    }

    public function getLayout($layout = null)
    {
        if (isset($layout)) {
            if (false !== ($layout = $this->loadLayout($layout))) {
                return $layout;
            }
        }

        if ($this->layout instanceof Layout) {
            return $this->layout;
        }

        return false;
    }

    public function setLayout($layout)
    {
        if (false !== ($layout = $this->loadLayout($layout))) {
            $this->layout = $layout;

            // add theme layout public directory
            loader()->addPublicDir($this->layout->getPath() . 'assets');

            presenter()->assets->autoload(
                [
                    'css' => ['layout'],
                    'js'  => ['layout'],
                ]
            );

            $partials = $this->layout->getPartials()->getArrayCopy();

            foreach ($partials as $offset => $partial) {
                presenter()->partials->addPartial($offset, $partial->getPathName());
            }

            return true;
        }

        return false;
    }

    protected function loadLayout($layout)
    {
        $extensions = ['.php', '.phtml', '.html', '.tpl'];

        if (isset($this->config[ 'extensions' ])) {
            array_unshift($partialsExtensions, $this->config[ 'extension' ]);
        } elseif (isset($this->config[ 'extension' ])) {
            array_unshift($extensions, $this->config[ 'extension' ]);
        }

        foreach ($extensions as $extension) {

            $extension = trim($extension, '.');

            if ($layout === 'theme') {
                $layoutFilePath = $this->getRealPath() . 'theme.' . $extension;
            } else {
                $layoutFilePath = $this->getRealPath() . 'layouts' . DIRECTORY_SEPARATOR . dash($layout) . DIRECTORY_SEPARATOR . 'layout.' . $extension;
            }

            if (is_file($layoutFilePath)) {
                return new Layout($layoutFilePath);
                break;
            }
        }

        return false;
    }
}