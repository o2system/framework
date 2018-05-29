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

namespace O2System\Framework\Libraries\Ui\Components\Form\Group;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Help
 *
 * @package O2System\Framework\Libraries\Ui\Components\Input
 */
class Help extends Element
{
    public function __construct($tagName = 'span')
    {
        parent::__construct($tagName);
        $this->attributes->addAttributeClass(['form-text', 'text-muted', 'form-help']);
    }
}