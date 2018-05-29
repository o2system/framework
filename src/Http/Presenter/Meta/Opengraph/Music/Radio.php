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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph\Music;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Meta\Opengraph\Audio;
use O2System\Framework\Http\Presenter\Meta\Opengraph\Basic;
use O2System\Framework\Http\Presenter\Meta\Opengraph\Image;
use O2System\Html\Document;

/**
 * Class Radio
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Music
 */
class Radio extends Basic
{
    /**
     * Radio::__construct
     *
     * @param \O2System\Html\Document $document
     */
    public function __construct(Document $document)
    {
        parent::__construct($document);

        $this->setType('music.radio_station');
    }

    // ------------------------------------------------------------------------

    /**
     * Radio::setSiteName
     *
     * @param string $name
     *
     * @return static
     */
    public function setSiteName($name)
    {
        $this->setMetadata('site_name', $name);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Radio::setUrl
     *
     * @param string $url
     *
     * @return static
     */
    public function setUrl($url)
    {
        if (strpos($url, 'http') !== false) {
            parent::setMetadata('url', $url);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Radio::createImage
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Image
     */
    public function createImage()
    {
        return new Image($this->ownerDocument);
    }

    // ------------------------------------------------------------------------

    /**
     * Radio::createAudio
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Audio
     */
    public function createAudio()
    {
        return new Audio($this->ownerDocument);
    }
}