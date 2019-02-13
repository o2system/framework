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

use MatthiasMullie\Minify\JS;
use O2System\Framework\Http\Presenter\Assets\Collections;

/**
 * Class Body
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions
 */
class Body extends Abstracts\AbstractPosition
{
    /**
     * Body::$javascript
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Javascript
     */
    protected $javascript;

    // ------------------------------------------------------------------------

    /**
     * Body::__construct
     */
    public function __construct()
    {
        $this->javascript = new Collections\Javascript();
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
        $unbundledFilename = ['app', 'app.min', 'theme', 'theme.min'];

        // Render js
        if ($this->javascript->count()) {
            $bundleJsContents = [];

            foreach ($this->javascript as $js) {
                if (in_array(pathinfo($js, PATHINFO_FILENAME), $unbundledFilename)) {
                    $jsVersion = $this->getVersion($js);
                    $output[] = '<script type="text/javascript" src="' . $this->getUrl($js) . '?v=' . $jsVersion . '"></script>';
                } else {
                    $bundleJsMap[ 'sources' ][] = $js;
                    $bundleJsContents[] = file_get_contents($js);
                }
            }

            // Bundled Js
            $bundleJsVersion = $this->getVersion(serialize($bundleJsContents));

            if(presenter()->page->file instanceof \SplFileInfo) {
                if(presenter()->page->file->getFilename() === 'index') {
                    $bundleJsFilename = 'body-' . presenter()->page->file->getDirectoryInfo()->getDirName();
                } else {
                    $bundleJsFilename = 'body-' . presenter()->page->file->getDirectoryInfo()->getDirName() . '-' . presenter()->page->file->getFilename();
                }
            } elseif(services()->has('controller')) {
                $bundleJsFilename = 'body-' . controller()->getParameter();

                if(controller()->getRequestMethod() !== 'index') {
                    $bundleJsFilename.= '-' . controller()->getRequestMethod();
                }
            } else {
                $bundleJsFilename = 'body-' . md5($bundleJsVersion);
            }

            $bundlePublicDir = modules()->current()->getPublicDir() . 'assets' . DIRECTORY_SEPARATOR;
            $bundleJsFilePath = $bundlePublicDir . $bundleJsFilename . '.js';
            $bundleJsMinifyFilePath = $bundlePublicDir . $bundleJsFilename . '.min.js';

            if (is_file($bundleJsFilePath . '.map')) {
                $bundleJsMap = json_decode(file_get_contents($bundleJsFilePath . '.map'), true);
                // if the file version is changed delete it first
                if ( ! hash_equals($bundleJsVersion, $bundleJsMap[ 'version' ])) {
                    unlink($bundleJsFilePath);
                    unlink($bundleJsFilePath . '.map');
                }
            }

            if ( ! is_file($bundleJsFilePath)) {
                $bundleJsMap[ 'version' ] = $bundleJsVersion;

                // Create css file
                if (count($bundleJsContents)) {
                    if ($bundleFileStream = @fopen($bundleJsFilePath, 'ab')) {
                        flock($bundleFileStream, LOCK_EX);
                        fwrite($bundleFileStream, implode(PHP_EOL, $bundleJsContents));
                        flock($bundleFileStream, LOCK_UN);
                        fclose($bundleFileStream);

                        // Create css map
                        if ($bundleFileStream = @fopen($bundleJsFilePath . '.map', 'ab')) {
                            flock($bundleFileStream, LOCK_EX);

                            fwrite($bundleFileStream, json_encode($bundleJsMap));

                            flock($bundleFileStream, LOCK_UN);
                            fclose($bundleFileStream);
                        }

                        // Create javascript file minify version
                        $minifyJsHandler = new JS($bundleJsFilePath);
                        $minifyJsHandler->minify($bundleJsMinifyFilePath);
                    }
                }
            }

            if(is_file($bundleJsFilePath)) {
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    $output[] = '<script type="text/javascript" src="' . $this->getUrl($bundleJsMinifyFilePath) . '?v=' . $bundleJsVersion . '"></script>';
                } else {
                    $output[] = '<script type="text/javascript" src="' . $this->getUrl($bundleJsFilePath) . '?v=' . $bundleJsVersion . '"></script>';
                }
            }
        }

        return implode(PHP_EOL, $output);
    }
}