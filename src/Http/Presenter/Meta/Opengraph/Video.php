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
use O2System\Framework\Http\Presenter\Meta\Opengraph\Traits\AuthorTrait;
use O2System\Framework\Http\Presenter\Meta\Opengraph\Traits\UrlTrait;

/**
 * Class Video
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Video extends AbstractNamespace
{
    use UrlTrait;
    use AuthorTrait;

    public $namespace = 'video';

    // ------------------------------------------------------------------------

    public function setMime($mime)
    {
        $this->setObject('type', $mime);

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setSize($width, $height)
    {
        if (is_numeric($width) AND is_numeric($height)) {
            $this->setObject('width', $width);
            $this->setObject('height', $height);
        }
    }

    // ------------------------------------------------------------------------

    public function setDuration($duration)
    {
        if (is_int($duration)) {
            $this->setObject('duration', $duration);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setReleaseDate($date)
    {
        $this->setObject('release_date', $date);

        return $this;
    }
}