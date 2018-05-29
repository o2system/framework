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
 * Class Audio
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Audio extends AbstractNamespace
{
    use UrlTrait;
    use AuthorTrait;

    public $namespace = 'audio';

    // ------------------------------------------------------------------------

    /**
     * Audio::setMime
     *
     * @param string $mime
     *
     * @return static
     */
    public function setMime($mime)
    {
        if (strpos($mime, 'audio/') !== false) {
            $this->setObject('type', $mime);
        }

        return $this;
    }
}