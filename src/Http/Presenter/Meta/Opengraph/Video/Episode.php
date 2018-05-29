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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph\Video;

// ------------------------------------------------------------------------

/**
 * Class Episode
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Video
 */
class Episode extends Movie
{
    public $namespace = 'video';

    // ------------------------------------------------------------------------

    public function setSeries(Series $series)
    {
        $this->merge($series->getArrayCopy());

        return $this;
    }
}