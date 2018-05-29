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

/**
 * Class Footer
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Footer extends Element
{
    public function __construct()
    {
        parent::__construct('div', 'heading');
        $this->attributes->addAttributeClass('panel-footer');
    }
}