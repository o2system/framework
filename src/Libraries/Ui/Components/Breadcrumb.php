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

use O2System\Framework\Libraries\Ui\Contents\Lists\Ordered;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Breadcrumb
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Breadcrumb extends Ordered
{
    public function __construct()
    {
        parent::__construct();

        $this->attributes->addAttributeClass('breadcrumb');
    }

    public function style($style)
    {
        if (in_array($style, ['arrow', 'dot', 'bar'])) {
            $this->attributes->removeAttributeClass('breadcrumb-*');
            $this->attributes->addAttributeClass('breadcrumb-' . $style);
        }

        return $this;
    }

    protected function pushChildNode(Element $node)
    {
        $node->attributes->addAttributeClass('breadcrumb-item');

        parent::pushChildNode($node);
    }
}