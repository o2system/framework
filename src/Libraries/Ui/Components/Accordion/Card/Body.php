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

namespace O2System\Framework\Libraries\Ui\Components\Accordion\Card;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Block
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Body extends \O2System\Framework\Libraries\Ui\Components\Card\Body
{
    public $collapse;

    public function __construct()
    {
        parent::__construct();

        $this->collapse = new Element('div', 'collapse');
        $this->collapse->attributes->addAttributeClass('collapse');
        $this->collapse->attributes->addAttribute('role', 'tabpanel');
    }

    public function render()
    {
        if ($this->title instanceof Element) {
            $this->title->attributes->addAttributeClass('card-title');
            $this->childNodes->push($this->title);
        }

        if ($this->subTitle instanceof Element) {
            $this->subTitle->attributes->addAttributeClass('card-subtitle');
            $this->childNodes->push($this->subTitle);
        }

        if ($this->paragraph instanceof Element) {
            $this->paragraph->attributes->addAttributeClass('card-text');
            $this->childNodes->push($this->paragraph);
        }

        if ($this->links instanceof ArrayIterator) {
            if ($this->links->count()) {
                foreach ($this->links as $link) {
                    $link->attributes->addAttributeClass('card-link');
                    $this->childNodes->push($link);
                }
            }
        }

        if ($this->hasChildNodes()) {
            $this->collapse->attributes->setAttributeId($this->attributes->getAttributeId());
            $this->attributes->removeAttribute('id');

            $body = new Element('div', 'body');
            $body->attributes = $this->attributes;
            $body->textContent = $this->textContent;
            $body->childNodes = $this->childNodes;

            $this->collapse->childNodes->push($body);

            return $this->collapse->render();
        }

        return '';
    }
}