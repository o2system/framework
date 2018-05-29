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
use O2System\Framework\Libraries\Ui\Traits\Collectors\ParagraphsCollectorTrait;

/**
 * Class Body
 *
 * @package O2System\Framework\Libraries\Ui\Components\Modal
 */
class Body extends Element
{
    use ParagraphsCollectorTrait;

    public function __construct()
    {
        parent::__construct('div', 'body');
        $this->attributes->addAttributeClass('modal-body');
    }

    public function render()
    {
        if ($this->hasParagraphs()) {
            foreach ($this->paragraphs as $paragraph) {
                $this->childNodes->push($paragraph);
            }
        }

        return parent::render();
    }
}