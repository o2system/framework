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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph\Traits;

// ------------------------------------------------------------------------

/**
 * Class UrlTrait
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Traits
 */
trait UrlTrait
{
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->offsetSet('og:' . $this->namespace, $url);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setSecureUrl($url)
    {
        if (strpos($url, 'https://') !== false) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $this->setObject('secure_url', $url);
            }
        }

        return $this;
    }
}