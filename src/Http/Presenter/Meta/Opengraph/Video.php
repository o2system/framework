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

    /**
     * Video::$namespace
     *
     * @var string
     */
    public $namespace = 'video';

    // ------------------------------------------------------------------------

    /**
     * Video::setMime
     *
     * @param string $mime
     *
     * @return static
     */
    public function setMime($mime)
    {
        $this->setObject('type', $mime);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Video::setSize
     *
     * @param int $width
     * @param int $height
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

    // ------------------------------------------------------------------------

    /**
     * Video::setDuration
     *
     * @param int $duration
     *
     * @return static
     */
    public function setDuration($duration)
    {
        if (is_int($duration)) {
            $this->setObject('duration', $duration);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Video::setReleaseDate
     *
     * @param string $date
     *
     * @return static
     */
    public function setReleaseDate($date)
    {
        $this->setObject('release_date', $date);

        return $this;
    }
}