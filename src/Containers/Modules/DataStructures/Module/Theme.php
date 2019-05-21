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

use O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Theme
 *
 * @package O2System\Framework\Containers\Modules\DataStructures\Module
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
     * Theme Presets
     *
     * @var array
     */
    private $presets = [];

    /**
     * Theme Layout
     *
     * @var Theme\Layout
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
        if (is_file($propFilePath = $dir . 'theme.json')) {
            $properties = json_decode(file_get_contents($propFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($properties[ 'config' ])) {
                    $this->presets = $properties[ 'presets' ];
                    unset($properties[ 'presets' ]);
                }

                $this->properties = $properties;
            }
        }

        // Set Default Theme Layout
        $this->setLayout('theme');
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::isValid
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
     * Theme::getParameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->getDirName();
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getCode
     *
     * @return string
     */
    public function getCode()
    {
        return strtoupper(substr(md5($this->getDirName()), 2, 7));
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getChecksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getProperties
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getPresets
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getPresets()
    {
        return new SplArrayObject($this->presets);
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getUrl
     *
     * @param string|null $path
     *
     * @return string
     */
    public function getUrl($path = null)
    {
        return path_to_url($this->getRealPath() . $path);
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::load
     *
     * @return static
     */
    public function load()
    {
        if ($this->getPresets()->offsetExists('assets')) {
            presenter()->assets->autoload($this->getPresets()->offsetGet('assets'));
        }

        presenter()->assets->loadCss('theme');
        presenter()->assets->loadJs('theme');

        // Autoload default theme layout
        $this->loadLayout();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::hasLayout
     *
     * @return bool
     */
    public function hasLayout($layout)
    {
        $extensions = ['.php', '.phtml', '.html', '.tpl'];

        if (isset($this->presets[ 'extensions' ])) {
            array_unshift($partialsExtensions, $this->presets[ 'extension' ]);
        } elseif (isset($this->presets[ 'extension' ])) {
            array_unshift($extensions, $this->presets[ 'extension' ]);
        }

        $found = false;
        foreach ($extensions as $extension) {
            $extension = trim($extension, '.');

            if ($layout === 'theme') {
                $layoutFilePath = $this->getRealPath() . 'theme.' . $extension;
            } else {
                $layoutFilePath = $this->getRealPath() . 'layouts' . DIRECTORY_SEPARATOR . dash($layout) . DIRECTORY_SEPARATOR . 'layout.' . $extension;
            }

            if (is_file($layoutFilePath)) {
                $found = true;
                break;
            }
        }

        return (bool)$found;
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::setLayout
     *
     * @param string $layout
     *
     * @return static
     */
    public function setLayout($layout)
    {
        $this->layout = $this->getLayout($layout);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::getLayout
     *
     * @param string $layout
     *
     * @return Theme\Layout
     */
    public function getLayout($layout = null)
    {
        if (isset($layout)) {
            $extensions = ['.php', '.phtml', '.html', '.tpl'];

            if (isset($this->presets[ 'extensions' ])) {
                array_unshift($partialsExtensions, $this->presets[ 'extension' ]);
            } elseif (isset($this->presets[ 'extension' ])) {
                array_unshift($extensions, $this->presets[ 'extension' ]);
            }

            foreach ($extensions as $extension) {
                $extension = trim($extension, '.');

                if ($layout === 'theme') {
                    $layoutFilePath = $this->getRealPath() . 'theme.' . $extension;
                } else {
                    $layoutFilePath = $this->getRealPath() . 'layouts' . DIRECTORY_SEPARATOR . dash($layout) . DIRECTORY_SEPARATOR . 'layout.' . $extension;
                }

                if (is_file($layoutFilePath)) {
                    return new Theme\Layout($layoutFilePath);
                    break;
                }
            }

            return false;
        }

        return $this->layout;
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::loadLayout
     */
    protected function loadLayout()
    {
        if ($this->layout instanceof Theme\Layout) {
            
            // load parent theme layout
            if($this->layout->getFilename() !== 'theme') {
                $themeLayout = $this->getLayout('theme');
                
                // add theme layout public directory
                loader()->addPublicDir($themeLayout->getPath() . 'assets');

                presenter()->assets->autoload(
                    [
                        'css' => ['layout'],
                        'js'  => ['layout'],
                    ]
                );

                $partials = $themeLayout->getPartials()->getArrayCopy();

                foreach ($partials as $offset => $partial) {
                    if ($partial instanceof SplFileInfo) {
                        presenter()->partials->addPartial($offset, $partial->getPathName());
                    }
                }
            }
            
            $this->loadChildLayout();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Theme::loadChildLayout
     */
    protected function loadChildLayout()
    {
        if ($this->layout instanceof Theme\Layout) {

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
                if ($partial instanceof SplFileInfo) {
                    presenter()->partials->addPartial($offset, $partial->getPathName());
                }
            }
        }
    }
}