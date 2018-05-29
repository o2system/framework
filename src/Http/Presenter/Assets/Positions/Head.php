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
    protected $font;
    protected $css;
    protected $javascript;

    public function __construct()
    {
        $this->font = new Collections\Font();
        $this->css = new Collections\Css();
        $this->javascript = new Collections\Javascript();
    }

    public function __toString()
    {
        $config = presenter()->getConfig('assets');

        $webpack = false;
        if (isset($config[ 'webpack' ])) {
            $webpack = (bool)$config[ 'webpack' ];
        }

        $output = [];

        // Render fonts
        if ($this->font->count()) {
            $minifyFontCollection = $this->css->getArrayCopy();
            $minifyFontKey = 'bundle-font-' . md5(serialize($minifyFontCollection));
            $minifyFontFile = PATH_PUBLIC . 'webpack' . DIRECTORY_SEPARATOR . $minifyFontKey . '.css';
            $minifyFontHandler = new CSS();

            foreach ($this->font as $font) {
                if ($webpack) {
                    $minifyFontHandler->add($font);
                } else {
                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl($font) . '">';
                }
            }

            if ($webpack) {
                if ( ! is_dir($minifyFontDirectory = dirname($minifyFontFile))) {
                    mkdir($minifyFontDirectory, 0777, true);
                }

                $minifyFontHandler->minify($minifyFontFile);

                $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl('webpack/' . $minifyFontKey . '.css') . '">';
            }
        }

        // Render css
        if ($this->css->count()) {
            $minifyCssCollection = $this->css->getArrayCopy();
            $minifyCssKey = 'bundle-head-' . md5(serialize($minifyCssCollection));
            $minifyCssFile = PATH_PUBLIC . 'webpack' . DIRECTORY_SEPARATOR . $minifyCssKey . '.css';
            $minifyCssHandler = new CSS();

            foreach ($this->css as $css) {
                if ($webpack) {
                    $minifyCssHandler->add($css);
                } else {
                    $url = $this->getUrl($css);
                    $url = str_replace('/.css', '/index.css', $url);

                    $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $url . '">';
                }
            }

            if ($webpack) {
                if ( ! is_dir($minifyCssDirectory = dirname($minifyCssFile))) {
                    mkdir($minifyCssDirectory, 0777, true);
                }

                $minifyCssHandler->minify($minifyCssFile);

                $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl('webpack/' . $minifyCssKey . '.css') . '">';
            }
        }

        // Render js
        if ($this->javascript->count()) {
            $minifyJsCollection = $this->javascript->getArrayCopy();
            $minifyJsKey = 'bundle-head-' . md5(serialize($minifyJsCollection));
            $minifyJsFile = PATH_PUBLIC . 'webpack' . DIRECTORY_SEPARATOR . $minifyJsKey . '.js';
            $minifyJsHandler = new JS();

            foreach ($this->javascript as $javascript) {
                if ($webpack) {
                    $minifyJsHandler->add($javascript);
                } else {
                    $url = $this->getUrl($javascript);
                    $url = str_replace('/.js', '/index.js', $url);

                    $output[] = '<script type="text/javascript" src="' . $url . '"></script>';
                }
            }

            if ($webpack) {
                if ( ! is_dir($minifyJsDirectory = dirname($minifyJsFile))) {
                    mkdir($minifyJsDirectory, 0777, true);
                }

                $minifyJsHandler->minify($minifyJsFile);

                $output[] = '<script type="text/javascript" src="' . $this->getUrl('webpack/' . $minifyJsKey . '.js') . '"></script>';
            }
        }

        return implode(PHP_EOL, $output);
    }
}