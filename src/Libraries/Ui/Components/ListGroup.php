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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Lists;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class ListGroup
 *
 * @package O2System\Framework\Libraries\Ui\Components\Lists
 */
class ListGroup extends Lists\Unordered
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes->addAttributeClass('list-group');
    }

    protected function pushChildNode(Element $node)
    {
        $node->attributes->addAttributeClass('list-group-item');

        if ($node instanceof Lists\Item) {
            $node->setContextualClassPrefix('list-group-item');
        }

        parent::pushChildNode($node);
    }
}