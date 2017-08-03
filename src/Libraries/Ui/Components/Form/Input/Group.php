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

use O2System\Framework\Libraries\Ui\Components;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Html\Element;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Group extends Element
{
    use SizingSetterTrait;

    public $input;
    public $addOns;

    public function __construct()
    {
        parent::__construct( 'div' );

        $this->attributes->addAttributeClass( 'input-group' );
        $this->attributes->addAttribute( 'role', 'group' );

        // Set input sizing class
        $this->setSizingClassPrefix( 'input-group' );

        $this->addOns = new Element\Nodes();
    }

    public function createInput( array $attributes = [] )
    {
        $field = new Components\Form\Input();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $field->attributes->addAttribute( $name, $value );

                if ( $name === 'name' ) {
                    $this->entity->setEntityName( 'input-' . $value );

                    if ( ! array_key_exists( 'id', $attributes ) ) {
                        $field->attributes->setAttributeId( 'input-' . $value );
                    }
                }
            }
        }

        $this->childNodes->push( $field );

        return $this->input = $this->childNodes->last();
    }

    public function createAddon( $node = null, $position = AddOn::ADDON_LEFT )
    {
        $addOn = new AddOn( $position );

        if ( isset( $node ) ) {
            if ( $node instanceof Element ) {
                $addOn->childNodes->push( $node );
            } else {
                $addOn->textContent->push( $node );
            }
        }

        $this->addOns->push( $addOn );

        return $this->addOns->last();
    }

    public function render()
    {
        $addOnsLeft = [];
        $addOnsRight = [];

        foreach( $this->addOns as $addOn ) {
            if( $addOn->position === AddOn::ADDON_LEFT ) {
                $addOnsLeft[] = $addOn;
            } else {
                $addOnsRight[] = $addOn;
            }
        }

        $output[] = $this->open();

        // AddOn Left
        if( count( $addOnsLeft ) ) {
            foreach( $addOnsLeft as $addOn ) {
                $output[] = $addOn;
            }
        }

        // Input
        $output[] = $this->input;

        // AddOn Right
        if( count( $addOnsRight ) ) {
            foreach( $addOnsRight as $addOn ) {
                $output[] = $addOn;
            }
        }

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}