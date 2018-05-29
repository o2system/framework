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
 * Class Music
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Music extends AbstractNamespace
{
    public $namespace = 'music';

    public function setDuration($duration)
    {
        $this->setObject('duration', (int)$duration);

        return $this;
    }

    public function setAlbum(Music\Album $album)
    {

    }
}