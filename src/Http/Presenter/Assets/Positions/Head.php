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

namespace O2System\Framework\Http\Presenter\Assets\Positions;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Assets\Collections;

/**
 * Class Head
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions
 */
class Head extends Abstracts\AbstractPosition
{
    /**
     * Head::$fonts
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Fonts
     */
    protected $fonts;

    /**
     * Head::$styles
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Styles
     */
    protected $styles;

    /**
     * Head::$javascripts
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Javascripts
     */
    protected $javascripts;

    // ------------------------------------------------------------------------

    /**
     * Head::__construct
     */
    public function __construct()
    {
        $this->fonts = new Collections\Fonts();
        $this->styles = new Collections\Styles();
        $this->javascripts = new Collections\Javascripts();
    }

    // ------------------------------------------------------------------------

    /**
     * Head::loadFile
     *
     * @param string      $filename
     * @param string|null $subDir
     *
     * @return void
     */
    public function loadFile($filename, $subDir = null)
    {
        if (is_file($filename)) {
            if (strpos($filename, 'font') !== false) {
                $this->fonts->append($filename);
            } else {
                $type = pathinfo($filename, PATHINFO_EXTENSION);

                switch ($type) {
                    case 'css':
                        $this->styles->append($filename);
                        break;

                    case 'js':
                        $this->javascripts->append($filename);
                        break;
                }
            }
        } elseif (isset($subDir)) {
            $type = pathinfo($filename, PATHINFO_EXTENSION);

            if (empty($type)) {
                $type = substr($subDir, 0, -1);
            }

            switch ($type) {
                case 'css':
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        if (false !== ($filePath = $this->getFilePath($filename . '.min.css', $subDir))) {
                            $this->styles->append($filePath);
                        }
                    } else {
                        if (false !== ($filePath = $this->getFilePath($filename . '.css', $subDir))) {
                            $this->styles->append($filePath);
                        }
                    }
                    break;

                case 'fonts':
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        if (false !== ($filePath = $this->getFilePath($filename . '.min.css', $subDir))) {
                            $this->fonts->append($filePath);
                        }
                    } else {
                        if (false !== ($filePath = $this->getFilePath($filename . '.css', $subDir))) {
                            $this->fonts->append($filePath);
                        }
                    }
                    break;

                case 'js':
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        if (false !== ($filePath = $this->getFilePath($filename . '.min.js', $subDir))) {
                            $this->styles->append($filePath);
                        }
                    } else {
                        if (false !== ($filePath = $this->getFilePath($filename . '.js', $subDir))) {
                            $this->styles->append($filePath);
                        }
                    }
                    break;
            }
        } else {
            $type = pathinfo($filename, PATHINFO_EXTENSION);

            if (empty($type)) {
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    // Search Style or Font
                    if (false !== ($filePath = $this->getFilePath($filename . '.min.css'))) {
                        $this->styles->append($filePath);
                    } elseif (false !== ($filePath = $this->getFilePath($filename . '.min.css'))) {
                        $this->fonts->append($filePath);
                    }

                    // Search Javascript
                    if (false !== ($filePath = $this->getFilePath($filename . '.min.js'))) {
                        $this->javascripts->append($filePath);
                    }
                } else {
                    // Search Style or Font
                    if (false !== ($filePath = $this->getFilePath($filename . '.css'))) {
                        $this->styles->append($filePath);
                    } elseif (false !== ($filePath = $this->getFilePath($filename . '.css'))) {
                        $this->fonts->append($filePath);
                    }

                    // Search Javascript
                    if (false !== ($filePath = $this->getFilePath($filename . '.js'))) {
                        $this->javascripts->append($filePath);
                    }
                }
            } else {
                switch ($type) {
                    case 'css':
                        if (false !== ($filePath = $this->getFilePath($filename))) {
                            $this->styles->append($filePath);
                        }
                        break;

                    case 'js':
                        if (false !== ($filePath = $this->getFilePath($filename))) {
                            $this->javascripts->append($filePath);
                        }
                        break;
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Head::__toString
     *
     * @return string
     */
    public function __toString()
    {
        $output = [];

        // Render fonts
        if ($this->fonts->count()) {

            $bundleFontsFile = $this->bundleFile('assets' . DIRECTORY_SEPARATOR . 'fonts.css',
                implode(PHP_EOL, $this->fonts));

            if (is_file($bundleFontsFile[ 'filePath' ])) {
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $bundleFontsFile[ 'url' ] . '?v=' . $bundleFontsFile[ 'version' ] . '">';
                } else {
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $bundleFontsFile[ 'url' ] . '?v=' . $bundleFontsFile[ 'version' ] . '">';
                }
            }
        }

        $unbundledFilenames = ['app', 'app.min', 'theme', 'theme.min'];

        if (presenter()->page->file instanceof \SplFileInfo) {
            if (presenter()->page->file->getFilename() === 'index') {
                $bundleFilename = 'head-' . presenter()->page->file->getDirectoryInfo()->getDirName();
            } else {
                $bundleFilename = 'head-' . presenter()->page->file->getDirectoryInfo()->getDirName() . '-' . presenter()->page->file->getFilename();
            }
        } elseif (services()->has('controller')) {
            $bundleFilename = 'head-' . controller()->getParameter();

            if (controller()->getRequestMethod() !== 'index') {
                $bundleFilename .= '-' . controller()->getRequestMethod();
            }
        } else {
            $bundleFilename = 'head-' . uniqid();
        }

        $bundleFilename = 'assets' . DIRECTORY_SEPARATOR . $bundleFilename;
        
        // Render style
        if ($this->styles->count()) {
            $bundleStyleSources = [];

            foreach ($this->styles as $style) {
                if (in_array(pathinfo($style, PATHINFO_FILENAME), $unbundledFilenames)) {
                    $fileVersion = $this->getVersion(filemtime($style));
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($style) . '?v=' . $fileVersion . '">';
                } elseif (in_array(pathinfo($style, PATHINFO_FILENAME), ['module', 'module.min'])) {
                    $modulePublicFile = $this->publishFile($style);
                    
                    if (is_file($modulePublicFile[ 'filePath' ])) {
                        if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                            $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $modulePublicFile[ 'minify' ][ 'url' ] . '?v=' . $modulePublicFile[ 'version' ] . '">';
                        } else {
                            $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $modulePublicFile[ 'url' ] . '?v=' . $modulePublicFile[ 'version' ] . '">';
                        }
                    }
                } else {
                    $bundleStyleSources[] = $style;
                }
            }

            if (count($bundleStyleSources)) {
                $bundleStylePublicFile = $this->bundleFile($bundleFilename . '.css', $bundleStyleSources);

                if (is_file($bundleStylePublicFile[ 'filePath' ])) {
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $bundleStylePublicFile[ 'minify' ][ 'url' ] . '?v=' . $bundleStylePublicFile[ 'version' ] . '">';
                    } else {
                        $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $bundleStylePublicFile[ 'url' ] . '?v=' . $bundleStylePublicFile[ 'version' ] . '">';
                    }
                }
            }
        }

        // Render javascript
        if ($this->javascripts->count()) {
            $bundleJavascriptSources = [];

            foreach ($this->javascripts as $javascript) {
                if (in_array(pathinfo($style, PATHINFO_FILENAME), $unbundledFilenames)) {
                    $fileVersion = $this->getVersion(filemtime($javascript));
                    $output[] = '<script type="text/javascript" id="js-'.pathinfo($javascript, PATHINFO_FILENAME).'" src="' . $this->getUrl($javascript) . '?v=' . $fileVersion . '"></script>';
                } elseif (in_array(pathinfo($javascript, PATHINFO_FILENAME), ['module', 'module.min'])) {
                    $modulePublicFile = $this->publishFile($javascript);
                    
                    if (is_file($modulePublicFile[ 'filePath' ])) {
                        if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                            $output[] = '<script type="text/javascript" id="js-module" src="' . $modulePublicFile[ 'minify' ][ 'url' ] . '?v=' . $modulePublicFile[ 'version' ] . '"></script>';
                        } else {
                            $output[] = '<script type="text/javascript" id="js-module" src="' . $modulePublicFile[ 'url' ] . '?v=' . $modulePublicFile[ 'version' ] . '"></script>';
                        }
                    }
                } else {
                    $bundleJavascriptSources[] = $javascript;
                }
            }

            if (count($bundleJavascriptSources)) {
                $bundleJavascriptPublicFile = $this->bundleFile($bundleFilename . '.js', $bundleJavascriptSources);

                if (is_file($bundleJavascriptPublicFile[ 'filePath' ])) {
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        $output[] = '<script type="text/javascript" id="js-bundle" src="' . $bundleJavascriptPublicFile[ 'minify' ][ 'url' ] . '?v=' . $bundleJavascriptPublicFile[ 'version' ] . '"></script>';
                    } else {
                        $output[] = '<script type="text/javascript" id="js-bundle" src="' . $bundleJavascriptPublicFile[ 'url' ] . '?v=' . $bundleJavascriptPublicFile[ 'version' ] . '"></script>';
                    }
                }
            }
        }

        return implode(PHP_EOL, $output);
    }
}