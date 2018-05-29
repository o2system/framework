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

use O2System\Framework\Libraries\Ui\Components\Modal\Dialog;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Modal
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Modal extends Element
{
    /**
     * @var Dialog
     */
    public $dialog;

    public function __construct()
    {
        parent::__construct('div', 'modal');
        $this->attributes->addAttributeClass('modal');
        $this->attributes->addAttribute('role', 'dialog');
        $this->attributes->addAttribute('tab-index', '-1');

        $this->childNodes->push(new Dialog());
        $this->dialog = $this->childNodes->last();
    }

    public function setTitle($text, $tagName = 'h5', array $attributes = [])
    {
        $this->dialog->content->header->tagName = $tagName;
        $this->dialog->content->header->textContent->push($text);

        if (count($this->attributes)) {
            foreach ($attributes as $name => $value) {
                $this->dialog->content->header->attributes->addAttribute($name, $value);
            }
        }

        return $this;
    }
}