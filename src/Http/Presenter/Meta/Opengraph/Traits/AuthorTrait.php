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

use O2System\Framework\Http\Presenter\Meta\Opengraph\Profile;

/**
 * Class AuthorTrait
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Traits
 */
trait AuthorTrait
{
    public function setAuthor($author, Profile $profile = null)
    {
        $this->setObject('author', $author);

        if (isset($profile)) {
            foreach ($profile->getArrayCopy() as $property => $element) {
                $property = $this->namespace . ':' . $profile->namespace . ':' . $property;
                $element->attributes[ 'name' ] = $property;
                $this->offsetSet($property, $element);
            }
        }
    }
}