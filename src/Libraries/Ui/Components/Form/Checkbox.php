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
 * Class Checkbox
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Checkbox extends Element
{
    public $input;

    public function __construct( $label, array $attributes = [] )
    {
        parent::__construct( 'div' );

        $this->attributes->addAttributeClass('checkbox');

        $attributes[ 'type' ] = 'checkbox';

        $label = new Element('label');
        $checkbox = new Input();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $checkbox->attributes->addAttribute( $name, $value );

                if ( $name === 'name' ) {
                    $this->entity->setEntityName( 'input-' . $value );
                    $label->entity->setEntityName( 'label-' . $value );
                    $label->attributes->addAttribute( 'for', $value );

                    if ( ! array_key_exists( 'id', $attributes ) ) {
                        $checkbox->attributes->setAttributeId( 'input-' . $value );
                    }
                }
            }
        }

        $this->input = $label->childNodes->push( $checkbox );

        $this->childNodes->push( $label );
    }
}