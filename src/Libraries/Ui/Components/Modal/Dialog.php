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

namespace O2System\Framework\Libraries\Ui\Components\Modal;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Modal\Dialog\Content;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Modal
 *
 * @package O2System\Framework\Libraries\Ui\Components\Modal
 */
class Dialog extends Element
{
    /**
     * @var Content
     */
    public $content;

    public function __construct()
    {
        parent::__construct('div', 'dialog');
        $this->attributes->addAttributeClass('modal-dialog');
        $this->attributes->addAttribute('role', 'document');

        $this->childNodes->push(new Content());
        $this->content = $this->childNodes->last();
    }
}