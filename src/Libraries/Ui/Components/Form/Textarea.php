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

use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Html\Element;

/**
 * Class Textarea
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Textarea extends Element
{
    use SizingSetterTrait;

    public function __construct( $attributes = [] )
    {
        parent::__construct( 'textarea' );
        $this->attributes->addAttributeClass( 'form-control' );

        $this->setSizingClassPrefix( 'form-control' );

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $this->attributes->addAttribute( $name, $value );
            }
        }
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