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

namespace O2System\Framework\Libraries\Ui\Components\Modal\Dialog\Content;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Collectors\ButtonsCollectorTrait;

/**
 * Class Footer
 *
 * @package O2System\Framework\Libraries\Ui\Components\Modal
 */
class Footer extends Element
{
    use ButtonsCollectorTrait;

    public function __construct()
    {
        parent::__construct('div', 'header');
        $this->attributes->addAttributeClass('modal-footer');
    }

    public function render()
    {
        if ($this->hasButtons()) {
            foreach ($this->buttons as $button) {
                $this->childNodes->push($button);
            }
        }

        return parent::render();
    }
}