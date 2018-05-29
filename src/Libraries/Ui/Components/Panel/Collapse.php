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

namespace O2System\Framework\Libraries\Ui\Components\Panel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Collectors\ParagraphsCollectorTrait;

/**
 * Class Collapse
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Collapse extends Element
{
    use ParagraphsCollectorTrait;

    public function __construct($id = null)
    {
        parent::__construct('div', 'body');

        $id = empty($id) ? 'panel-collapse-' . mt_rand(0, 1000) : $id;

        $this->attributes->setAttributeId($id);
        $this->attributes->addAttributeClass(['panel-collapse', 'collapse']);
    }

    public function in()
    {
        $this->attributes->removeAttributeClass('out');
        $this->attributes->addAttributeClass('in');

        return $this;
    }

    public function out()
    {
        $this->attributes->removeAttributeClass('in');
        $this->attributes->addAttributeClass('out');

        return $this;
    }
}