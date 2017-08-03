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

namespace O2System\Framework\Libraries\Ui\Components\Form\Input;

// ------------------------------------------------------------------------

use O2System\Html\Element;

/**
 * Class Input
 *
 * @package O2System\Framework\Libraries\Ui\Components\Input
 */
class Field extends Element
{
    public function __construct( $type = 'text' )
    {
        parent::__construct( 'input' );

        $this->attributes->addAttribute( 'type', $type );
        $this->attributes->addAttributeClass( 'form-control' );
    }

    public function setName( $name )
    {
        $name = dash( $name );
        $this->entity->setEntityName( $name );
        $this->attributes->addAttribute( 'name', $name );

        return $this;
    }

    public function setPlaceholder( $placeholder )
    {
        $this->attributes->addAttribute( 'placeholder', $placeholder );

        return $this;
    }

    public function disabled()
    {
        $this->attributes->addAttribute( 'disabled', 'disabled' );

        return $this;
    }

    public function readOnly()
    {
        $this->attributes->addAttribute( 'readonly', 'readonly' );

        return $this;
    }
}