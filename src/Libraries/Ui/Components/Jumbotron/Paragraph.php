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

namespace O2System\Framework\Libraries\Ui\Components\Jumbotron;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Paragraph
 *
 * @package O2System\Framework\Libraries\Ui\Components\Jumbotron
 */
class Paragraph extends Element
{
    public function __construct()
    {
        parent::__construct('p');
    }

    public function createLink($label, $href = null)
    {
        if ( ! $this->attributes->hasAttributeClass('lead')) {
            $this->attributes->addAttributeClass('lead');
        }

        $link = new Link($label, $href);
        $link->attributes->addAttributeClass(['btn', 'btn-primary', 'btn-lg']);
        $this->childNodes->push($link);

        return $this->childNodes->last();
    }
}