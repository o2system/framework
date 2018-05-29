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

namespace O2System\Framework\Libraries\Ui\Traits\Collectors;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class LinksCollectorTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Collectors
 */
trait LinksCollectorTrait
{
    public $links;

    public function createLink($label, $href = null)
    {
        $link = new Link($label, $href);

        if ( ! $this->links instanceof ArrayIterator) {
            $this->links = new ArrayIterator();
        }

        $this->links->push($link);
    }

    public function addLink(Link $link)
    {
        if ( ! $this->links instanceof ArrayIterator) {
            $this->links = new ArrayIterator();
        }

        $this->links->push($link);

        return $this;
    }
}