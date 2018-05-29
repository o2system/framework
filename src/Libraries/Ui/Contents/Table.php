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

namespace O2System\Framework\Libraries\Ui\Contents;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Table\Body;
use O2System\Framework\Libraries\Ui\Contents\Table\Footer;
use O2System\Framework\Libraries\Ui\Contents\Table\Header;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Table
 *
 * @package O2System\Framework\Libraries\Ui\Contents
 */
class Table extends Element
{
    use SizingSetterTrait;

    public $header;
    public $body;
    public $footer;

    protected $responsive = false;

    public function __construct()
    {
        parent::__construct('table');
        $this->attributes->addAttributeClass('table');

        $this->setSizingClassPrefix('table');

        $this->header = new Header();
        $this->body = new Body();
        $this->footer = new Footer();
    }

    public function responsive()
    {
        $this->responsive = true;

        return $this;
    }

    public function addHoverEffect()
    {
        $this->attributes->addAttributeClass('table-hover');

        return $this;
    }

    public function striped()
    {
        $this->attributes->addAttributeClass('table-striped');

        return $this;
    }

    public function bordered()
    {
        $this->attributes->addAttributeClass('table-bordered');

        return $this;
    }

    public function condensed()
    {
        $this->attributes->addAttributeClass('table-condensed');

        return $this;
    }

    public function dark()
    {
        $this->attributes->removeAttributeClass('table-light');
        $this->attributes->addAttributeClass('table-dark');

        return $this;
    }

    public function light()
    {
        $this->attributes->removeAttributeClass('table-dark');
        $this->attributes->addAttributeClass('table-light');

        return $this;
    }

    public function render()
    {
        if ($this->header->hasChildNodes()) {
            $this->childNodes->push($this->header);
        }

        if ($this->body->hasChildNodes()) {
            $this->childNodes->push($this->body);
        }

        if ($this->footer->hasChildNodes()) {
            $this->childNodes->push($this->footer);
        }

        if ($this->responsive) {
            $this->attributes->addAttributeClass('table-responsive');
        }

        return parent::render();
    }
}