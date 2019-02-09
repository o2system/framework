<?php
/**
 * This file is part of the O2System Framework package.
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

    /**
     * Table::$header
     *
     * @var \O2System\Framework\Libraries\Ui\Contents\Table\Header
     */
    public $header;

    /**
     * Table::$body
     *
     * @var \O2System\Framework\Libraries\Ui\Contents\Table\Body
     */
    public $body;

    /**
     * Table::$footer
     *
     * @var \O2System\Framework\Libraries\Ui\Contents\Table\Footer
     */
    public $footer;

    /**
     * Table::$responsive
     *
     * @var bool
     */
    protected $responsive = false;

    // ------------------------------------------------------------------------

    /**
     * Table::__construct
     */
    public function __construct()
    {
        parent::__construct('table');
        $this->attributes->addAttributeClass('table');

        $this->setSizingClassPrefix('table');

        $this->header = new Header();
        $this->body = new Body();
        $this->footer = new Footer();
    }

    // ------------------------------------------------------------------------

    /**
     * Table::responsive
     *
     * @return static
     */
    public function responsive()
    {
        $this->responsive = true;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::addHoverEffect
     *
     * @return static
     */
    public function addHoverEffect()
    {
        $this->attributes->addAttributeClass('table-hover');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::striped
     *
     * @return static
     */
    public function striped()
    {
        $this->attributes->addAttributeClass('table-striped');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::bordered
     *
     * @return static
     */
    public function bordered()
    {
        $this->attributes->addAttributeClass('table-bordered');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::condensed
     *
     * @return static
     */
    public function condensed()
    {
        $this->attributes->addAttributeClass('table-condensed');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::dark
     *
     * @return static
     */
    public function dark()
    {
        $this->attributes->removeAttributeClass('table-light');
        $this->attributes->addAttributeClass('table-dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::light
     *
     * @return static
     */
    public function light()
    {
        $this->attributes->removeAttributeClass('table-dark');
        $this->attributes->addAttributeClass('table-light');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::render
     *
     * @return string
     */
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