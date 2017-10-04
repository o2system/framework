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

use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\BorderSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\PopoverSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ShapeClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TooltipSetterTrait;
use O2System\Html\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;

/**
 * Class Button
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Button extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use BorderSetterTrait;
    use SizingSetterTrait;
    use PopoverSetterTrait;
    use TooltipSetterTrait;

    public $icon;

    public function __construct( $label = null, $contextualClass = 'default' )
    {
        parent::__construct( 'button' );

        $this->attributes->addAttribute( 'type', 'button' );
        $this->attributes->addAttributeClass( 'btn' );

        // Set button contextual class
        $this->setContextualClassPrefix( 'btn' );
        $this->setContextualClassSuffix( $contextualClass );

        $this->setSizingClassPrefix( 'btn' );

        if ( isset( $label ) ) {
            $this->setLabel( $label );
        }
    }

    public function setLabel( $label )
    {
        $this->textContent->prepend( $label );
        $this->entity->setEntityName( $label );

        return $this;
    }

    public function setIcon( $icon )
    {
        if ( $icon instanceof Icon ) {
            $this->icon = $icon;
        } else {
            $this->icon = new Icon( $icon );
        }

        return $this;
    }

    public function active()
    {
        $this->attributes->addAttributeClass( 'active' );

        return $this;
    }

    public function disabled()
    {
        $this->attributes->addAttribute( 'disabled', 'disabled' );
        $this->attributes->addAttributeClass( 'disabled' );
        $this->attributes->addAttribute( 'aria-disabled', true );

        return $this;
    }

    public function autofocus()
    {
        $this->attributes->addAttribute( 'autofocus', 'autofocus' );

        return $this;
    }

    public function render()
    {
        if ( $this->icon instanceof Icon and ! $this->hasTextContent() ) {
            $this->attributes->addAttributeClass( [ 'btn-icon', 'btn-icon-mini' ] );
        }

        $output[] = $this->open();

        if ( $this->icon instanceof Icon ) {
            $output[] = $this->icon;
        }

        if ( $this->hasTextContent() ) {
            $output[] = implode( '', $this->textContent->getArrayCopy() );
        }

        if ( $this->hasChildNodes() ) {
            $output[] = implode( PHP_EOL, $this->childNodes->getArrayCopy() );
        }


        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}