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

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use O2System\Framework\Http\Presenter\Assets\Collections;

/**
 * Class Head
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions
 */
class Head extends Abstracts\AbstractPosition
{
    /**
     * Head::$font
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Font
     */
    protected $font;

    /**
     * Head::$css
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Css
     */
    protected $css;

    /**
     * Head::$javascript
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Collections\Javascript
     */
    protected $javascript;

    // ------------------------------------------------------------------------

    /**
     * Head::__construct
     */
    public function __construct()
    {
        $this->font = new Collections\Font();
        $this->css = new Collections\Css();
        $this->javascript = new Collections\Javascript();
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
        if ($this->font->count()) {

            $bundleFontCollection = $this->font->getArrayCopy();
            $bundleFontVersion = $this->getVersion(serialize($bundleFontCollection));
            $bundleFontFilePath = PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR . 'fonts.css';
            $bundleFontMinifyFilePath = PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR . 'fonts.min.css';

            if (is_file($bundleFontFilePath . '.map')) {
                $bundleFontMap = json_decode(file_get_contents($bundleFontFilePath . '.map'), true);
                // if the file version is changed delete it first
                if ( ! hash_equals($bundleFontVersion, $bundleFontMap[ 'version' ])) {
                    unlink($bundleFontFilePath);
                    unlink($bundleFontFilePath . '.map');
                }
            }

            if ( ! is_file($bundleFontFilePath)) {
                $bundleFontMap = [
                    'version' => $bundleFontVersion,
                ];

                // Create css font file
                if ($bundleFileStream = @fopen($bundleFontFilePath, 'ab')) {
                    flock($bundleFileStream, LOCK_EX);

                    foreach ($this->font as $font) {
                        $bundleFontMap[ 'sources' ][] = $font;
                        fwrite($bundleFileStream, file_get_contents($font));
                    }

                    flock($bundleFileStream, LOCK_UN);
                    fclose($bundleFileStream);
                }

                // Create css font map
                if ($bundleFileStream = @fopen($bundleFontFilePath . '.map', 'ab')) {
                    flock($bundleFileStream, LOCK_EX);

                    fwrite($bundleFileStream, json_encode($bundleFontMap));

                    flock($bundleFileStream, LOCK_UN);
                    fclose($bundleFileStream);
                }

                // Create css font file minify version
                $minifyFontHandler = new CSS($bundleFontFilePath);
                $minifyFontHandler->minify($bundleFontMinifyFilePath);
            }

            if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($bundleFontMinifyFilePath) . '?v=' . $bundleFontVersion . '">';
            } else {
                $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($bundleFontFilePath) . '?v=' . $bundleFontVersion . '">';
            }
        }

        $unbundledFilename = ['app', 'app.min', 'theme', 'theme.min'];

        // Render css
        if ($this->css->count()) {
            $bundleCssContents = [];

            foreach ($this->css as $css) {
                if (in_array(pathinfo($css, PATHINFO_FILENAME), $unbundledFilename)) {
                    $cssVersion = $this->getVersion($css);
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($css) . '?v=' . $cssVersion . '">';
                } else {
                    $bundleCssMap[ 'sources' ][] = $css;
                    $bundleCssContents[] = file_get_contents($css);
                }
            }

            // Bundled Css
            $bundleCssVersion = $this->getVersion(serialize($bundleCssContents));

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
                $bundleJsFilename = 'body-' . md5($bundleCssVersion);
            }

            $bundlePublicDir = modules()->current()->getPublicDir() . 'assets' . DIRECTORY_SEPARATOR;
            $bundleCssFilePath = $bundlePublicDir . $bundleJsFilename . '.css';
            $bundleCssMinifyFilePath = $bundlePublicDir . $bundleJsFilename . '.min.css';

            if (is_file($bundleCssFilePath . '.map')) {
                $bundleCssMap = json_decode(file_get_contents($bundleCssFilePath . '.map'), true);
                // if the file version is changed delete it first
                if ( ! hash_equals($bundleCssVersion, $bundleCssMap[ 'version' ])) {
                    unlink($bundleCssFilePath);
                    unlink($bundleCssFilePath . '.map');
                }
            }

            if ( ! is_file($bundleCssFilePath)) {
                $bundleCssMap[ 'version' ] = $bundleCssVersion;

                // Create css file
                if (count($bundleCssContents)) {
                    if ($bundleFileStream = @fopen($bundleCssFilePath, 'ab')) {
                        flock($bundleFileStream, LOCK_EX);
                        fwrite($bundleFileStream, implode(PHP_EOL, $bundleCssContents));
                        flock($bundleFileStream, LOCK_UN);
                        fclose($bundleFileStream);

                        // Create css map
                        if ($bundleFileStream = @fopen($bundleCssFilePath . '.map', 'ab')) {
                            flock($bundleFileStream, LOCK_EX);

                            fwrite($bundleFileStream, json_encode($bundleCssMap));

                            flock($bundleFileStream, LOCK_UN);
                            fclose($bundleFileStream);
                        }

                        // Create css file minify version
                        $minifyCssHandler = new CSS($bundleCssFilePath);
                        $minifyCssHandler->minify($bundleCssMinifyFilePath);
                    }
                }
            }

            // Add link css
            if (is_file($bundleCssFilePath)) {
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($bundleCssMinifyFilePath) . '?v=' . $bundleCssVersion . '">';
                } else {
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($bundleCssFilePath) . '?v=' . $bundleCssVersion . '">';
                }
            }
        }

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