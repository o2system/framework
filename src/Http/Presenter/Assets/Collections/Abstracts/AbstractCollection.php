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

namespace O2System\Framework\Http\Presenter\Assets\Collections\Abstracts;

// ------------------------------------------------------------------------

use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class AbstractCollection
 * @package O2System\Framework\Http\Presenter\Assets\Collections
 */
abstract class AbstractCollection extends ArrayIterator
{
    public function append($value)
    {
        if ( ! $this->has($value)) {
            parent::append($value);
        }
    }
}