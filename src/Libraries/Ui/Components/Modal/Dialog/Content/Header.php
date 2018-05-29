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

/**
 * Class Header
 *
 * @package O2System\Framework\Libraries\Ui\Components\Modal
 */
class Header extends Element
{
    public $title;
    public $button;

    public function __construct()
    {
        parent::__construct('div', 'header');
        $this->attributes->addAttributeClass('modal-header');

        $title = new Element('h5', 'title');
        $title->attributes->addAttributeClass('modal-title');

        $this->childNodes->push($title);
        $this->title = $this->childNodes->last();

        $button = new Element('button', 'button');
        $button->attributes->addAttribute('type', 'button');
        $button->attributes->addAttributeClass('close');
        $button->attributes->addAttribute('data-dismiss', 'modal');
        $button->attributes->addAttribute('aria-label', 'Close');

        $span = new Element('span');
        $span->attributes->addAttribute('aria-hidden', true);
        $span->textContent->push('&times;');

        $button->childNodes->push($span);

        $this->childNodes->push($button);
        $this->button = $this->childNodes->last();
    }

    public function setTitle($text)
    {
        $this->title->textContent->replace($text);
    }
}