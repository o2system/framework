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

use MatthiasMullie\Minify\JS;
use O2System\Framework\Http\Presenter\Assets\Collections;

/**
 * Class Body
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions
 */
class Body extends Abstracts\AbstractPosition
{
    protected $javascript;

    public function __construct()
    {
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

        // Render js
        if ($this->javascript->count()) {
            $minifyJsCollection = $this->javascript->getArrayCopy();
            $minifyJsKey = 'bundle-body-' . md5(serialize($minifyJsCollection));
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