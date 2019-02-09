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
            foreach ($this->javascript as $js) {
                if (in_array(pathinfo($js, PATHINFO_FILENAME), $unbundledFilename)) {
                    $jsVersion = $this->getVersion($js);
                    $output[] = '<script type="text/javascript" src="' . $this->getUrl($js) . '?v=' . $jsVersion . '"></script>';
                }
            }

            $bundleJsCollection = $this->javascript->getArrayCopy();
            $bundleJsVersion = $this->getVersion(serialize($bundleJsCollection));
            $bundleJsFilename = 'body-' . controller()->getClassInfo()->getParameter();
            $bundleJsFilePath = PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $bundleJsFilename . '.js';
            $bundleJsMinifyFilePath = PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $bundleJsFilename . '.min.js';

            if (is_file($bundleJsFilePath . '.map')) {
                $bundleJsMap = json_decode(file_get_contents($bundleJsFilePath . '.map'), true);
                // if the file version is changed delete it first
                if ( ! hash_equals($bundleJsVersion, $bundleJsMap[ 'version' ])) {
                    unlink($bundleJsFilePath);
                    unlink($bundleJsFilePath . '.map');
                }
            }

            if ( ! is_file($bundleJsFilePath)) {
                $bundleJsMap = [
                    'version' => $bundleJsVersion,
                ];

                // Create js file
                if ($bundleFileStream = @fopen($bundleJsFilePath, 'ab')) {
                    flock($bundleFileStream, LOCK_EX);

                    foreach ($this->javascript as $javascript) {
                        if ( ! in_array(pathinfo($javascript, PATHINFO_FILENAME), $unbundledFilename)) {
                            $bundleJsMap[ 'sources' ][] = $javascript;
                            fwrite($bundleFileStream, file_get_contents($javascript));
                        }
                    }

                    flock($bundleFileStream, LOCK_UN);
                    fclose($bundleFileStream);
                }

                // Create js map
                if ($bundleFileStream = @fopen($bundleJsFilePath . '.map', 'ab')) {
                    flock($bundleFileStream, LOCK_EX);

                    fwrite($bundleFileStream, json_encode($bundleJsMap));

                    flock($bundleFileStream, LOCK_UN);
                    fclose($bundleFileStream);
                }

                // Create js file minify version
                $minifyJsHandler = new JS($bundleJsFilePath);
                $minifyJsHandler->minify($bundleJsMinifyFilePath);
            }

            if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                $output[] = '<script type="text/javascript" src="' . $this->getUrl($bundleJsMinifyFilePath) . '?v=' . $bundleJsVersion . '"></script>';
            } else {
                $output[] = '<script type="text/javascript" src="' . $this->getUrl($bundleJsFilePath) . '?v=' . $bundleJsVersion . '"></script>';
            }
        }

        return implode(PHP_EOL, $output);
    }
}