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

use O2System\Framework\Libraries\Ui\Components\Table\Body;
use O2System\Framework\Libraries\Ui\Components\Table\Footer;
use O2System\Framework\Libraries\Ui\Components\Table\Header;
use O2System\Html\Element;

/**
 * Class Table
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Table extends Element
{
    public $header;
    public $body;
    public $footer;

    protected $responsive = false;

    public function __construct()
    {
        parent::__construct( 'table' );
        $this->attributes->addAttributeClass( 'table' );

        $this->header = new Header();
        $this->body = new Body();
        $this->footer = new Footer();
    }

    public function responsive()
    {
        $this->responsive  = true;

        return $this;
    }

    public function addHoverEffect()
    {
        $this->attributes->addAttributeClass( 'table-hover' );

        return $this;
    }

    public function striped()
    {
        $this->attributes->addAttributeClass( 'table-striped' );

        return $this;
    }

    public function bordered()
    {
        $this->attributes->addAttributeClass( 'table-bordered' );

        return $this;
    }

    public function condensed()
    {
        $this->attributes->addAttributeClass( 'table-condensed' );

        return $this;
    }

    public function render()
    {
        if ( $this->header->hasChildNodes() ) {
            $this->childNodes->push( $this->header );
        }

        if ( $this->body->hasChildNodes() ) {
            $this->childNodes->push( $this->body );
        }

        if ( $this->footer->hasChildNodes() ) {
            $this->childNodes->push( $this->footer );
        }

        if( $this->responsive ) {
            $container = new Element('div');
            $container->attributes->addAttributeClass('table-responsive');
            $container->textContent->push( parent::render() );

            return $container->render();
        }

        return parent::render();
    }
}