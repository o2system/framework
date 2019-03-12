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
 * Class Body
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions
 */
class Body extends Abstracts\AbstractPosition
{
    /**
     * Body::$javascripts
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Javascripts
     */
    protected $javascripts;

    // ------------------------------------------------------------------------

    /**
     * Body::__construct
     */
    public function __construct()
    {
        $this->javascripts = new Collections\Javascripts();
    }

    // ------------------------------------------------------------------------

    /**
     * Body::loadFile
     *
     * @param string      $filename
     * @param string|null $subDir
     *
     * @return void
     */
    public function loadFile($filename, $subDir = null)
    {
        if (is_file($filename)) {
            $this->javascripts->append($filename);
        } else {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            if (empty($extension)) {
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    if (false !== ($filePath = $this->getFilePath($filename . '.min.js'))) {
                        $this->javascripts->append($filePath);
                    }
                } else {
                    if (false !== ($filePath = $this->getFilePath($filename . '.js', $subDir))) {
                        $this->javascripts->append($filePath);
                    }
                }
            } elseif (false !== ($filePath = $this->getFilePath($filename, $subDir))) {
                $this->javascripts->append($filePath);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Body::__toString
     *
     * @return string
     */
    public function __toString()
    {
        $output = [];
        $unbundledFilenames = ['app', 'app.min', 'theme', 'theme.min'];

        if (presenter()->page->file instanceof \SplFileInfo) {
            if (presenter()->page->file->getFilename() === 'index') {
                $bundleFilename = 'body-' . presenter()->page->file->getDirectoryInfo()->getDirName();
            } else {
                $bundleFilename = 'body-' . presenter()->page->file->getDirectoryInfo()->getDirName() . '-' . presenter()->page->file->getFilename();
            }
        } elseif (services()->has('controller')) {
            $bundleFilename = 'body-' . controller()->getParameter();

            if (controller()->getRequestMethod() !== 'index') {
                $bundleFilename .= '-' . controller()->getRequestMethod();
            }
        } else {
            $bundleFilename = 'body-' . uniqid();
        }

        $bundleFilename = 'assets' . DIRECTORY_SEPARATOR . $bundleFilename;

        // Render js
        if ($this->javascripts->count()) {
            $bundleJavascriptSources = [];

            foreach ($this->javascripts as $javascript) {
                if (in_array(pathinfo($javascript, PATHINFO_FILENAME), $unbundledFilenames)) {
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