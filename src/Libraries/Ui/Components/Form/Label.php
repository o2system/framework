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

namespace O2System\Framework\Libraries\Ui\Components\Form;

// ------------------------------------------------------------------------

use O2System\Html\Element;

/**
 * Class Label
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Label extends Element
{
    public function __construct()
    {
        parent::__construct( 'label' );
        $this->attributes->addAttributeClass( 'form-label-control' );
    }

    public function screenReaderOnly()
    {
        $this->attributes->removeAttributeClass( 'form-label-control' );
        $this->attributes->addAttributeClass( 'sr-only' );

        return $this;
    }

    public function render()
    {
        $output[] = $this->open();

        if ( $this->hasChildNodes() ) {
            $output[] = implode( PHP_EOL, $this->childNodes->getArrayCopy() );
        }

        if ( $this->hasTextContent() ) {
            $output[] = implode( '', $this->textContent->getArrayCopy() );
        }

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}