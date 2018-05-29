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

namespace O2System\Framework\Libraries\Ui\Components\Card;

// ------------------------------------------------------------------------

/**
 * Class Deck
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Deck extends Group
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes->removeAttributeClass('card-group');
        $this->attributes->addAttributeClass('card-deck');
    }
}