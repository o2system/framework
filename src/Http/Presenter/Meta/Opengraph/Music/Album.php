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

use O2System\Framework\Http\Presenter\Meta\Opengraph\Abstracts\AbstractNamespace;

/**
 * Class Album
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Music
 */
class Album extends AbstractNamespace
{
    public $namespace = 'music:album';

    // ------------------------------------------------------------------------

    /**
     * Album::setDisc
     *
     * @param int $trackNumber
     *
     * @return static
     */
    public function setDisc($discNumber)
    {
        if (is_numeric($discNumber)) {
            $this->setObject('disc', $discNumber);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Album::setTrack
     *
     * @param int $trackNumber
     *
     * @return static
     */
    public function setTrack($trackNumber)
    {
        if (is_numeric($trackNumber)) {
            $this->setObject('track', $trackNumber);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setMusician(Musician $musician)
    {
        $this->merge($musician->getArrayCopy());

        return $this;
    }

    public function setReleaseDate($releaseDate)
    {
        $this->setObject('release_date', $releaseDate);

        return $this;
    }
}