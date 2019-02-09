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

namespace O2System\Framework\Libraries\Ui\Components\Modal\Dialog;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Modal\Dialog\Content\Body;
use O2System\Framework\Libraries\Ui\Components\Modal\Dialog\Content\Footer;
use O2System\Framework\Libraries\Ui\Components\Modal\Dialog\Content\Header;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Content
 *
 * @package O2System\Framework\Libraries\Ui\Components\Modal
 */
class Content extends Element
{
    /**
     * Content::$header
     *
     * @var Content\Header
     */
    public $header;

    /**
     * Content::$body
     *
     * @var Content\Body
     */
    public $body;

    /**
     * Content::$footer
     *
     * @var Content\Footer
     */
    public $footer;

    // ------------------------------------------------------------------------

    /**
     * Content::__construct
     */
    public function __construct()
    {
        parent::__construct('div', 'content');
        $this->attributes->addAttributeClass('modal-content');

        $this->childNodes->push(new Header());
        $this->header = $this->childNodes->last();

        $this->childNodes->push(new Body());
        $this->body = $this->childNodes->last();

        $this->childNodes->push(new Footer());
        $this->footer = $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Content::render
     *
     * @return string
     */
    public function render()
    {
        if ( ! $this->footer->hasChildNodes() && ! $this->footer->hasTextContent() && ! $this->footer->hasButtons()) {
            $this->childNodes->pop();
        }

        return parent::render();
    }
}