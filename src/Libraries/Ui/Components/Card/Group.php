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

use O2System\Framework\Libraries\Ui\Components\Card;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Group extends Element
{
    public function __construct()
    {
        parent::__construct('div', 'group');
        $this->attributes->addAttributeClass('card-group');
    }

    /**
     * @param string $contextualClass
     * @param bool   $inverse
     *
     * @return Card
     */
    public function createCard($contextualClass = Card::DEFAULT_CONTEXT, $inverse = false)
    {
        $card = new Card($contextualClass, $inverse);
        $this->childNodes->push($card);

        return $this->childNodes->last();
    }

    public function addCard(Card $card)
    {
        $this->childNodes->push($card);

        return $this;
    }
}