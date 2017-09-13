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
 * Class Select
 *
 * @package O2System\Framework\Libraries\Ui\Components\Input
 */
class Select extends Element
{
    use Select\Traits\OptionCreateTrait;

    public function __construct()
    {
        parent::__construct( 'select' );
        $this->attributes->addAttributeClass( 'form-control' );
    }

    public function createGroup( $label )
    {
        $group = new Select\Group();
        $group->textContent->push( $label );

        $this->childNodes->push( $group );

        return $this->childNodes->last();
    }

    public function multiple()
    {
        $this->attributes->addAttribute( 'multiple', 'multiple' );

        return $this;
    }
}