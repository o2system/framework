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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Meta\Opengraph\Abstracts\AbstractNamespace;

/**
 * Class Image
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Image extends AbstractNamespace
{
    public $namespace = 'image';

    /**
     * Image::setMime
     *
     * @param string $mime Image mime type.
     *
     * @return static
     */
    public function setMime($mime)
    {
        if (strpos($mime, 'image/') !== false) {
            $this->setObject('type', $mime);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::setSize
     *
     * @param int $width  Image width
     * @param int $height Image Height
     *
     * @return static
     */
    public function setSize($width, $height)
    {
        if (is_numeric($width) AND is_numeric($height)) {
            $this->setObject('width', $width);
            $this->setObject('height', $height);
        }

        return $this;
    }
}