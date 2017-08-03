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

use O2System\Framework\Libraries\Ui\Components;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Html\Element;

/**
 * Class Fieldset
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Fieldset extends Group
{
    public $legend;

    public function __construct( $contextualClass = self::DEFAULT_CONTEXT )
    {
        parent::__construct( $contextualClass );

        $this->tagName = 'fieldset';
        $this->attributes->addAttributeClass( 'form-group' );
        $this->attributes->addAttribute( 'role', 'group' );

        // Set input sizing class
        $this->setSizingClassPrefix( 'form-group' );

        $this->setContextualClassPrefix( 'has' );
        if ( $contextualClass !== self::DEFAULT_CONTEXT ) {
            $this->setContextualClassSuffix( $contextualClass );
        }
    }

    public function createLegend( $text, $attributes = [] )
    {
        $element = new Element( 'legend', 'legend-' . $text );
        $element->attributes->addAttribute( 'for', dash( $text ) );

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $element->attributes->addAttribute( $name, $value );
            }
        }

        $element->textContent->push( $text );

        $this->childNodes->push( $element );

        return $this->label = $this->childNodes->last();
    }

    public function disabled()
    {
        $this->attributes->addAttribute( 'disabled', 'disabled' );

        return $this;
    }
}