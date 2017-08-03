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

use O2System\Html\Element;

/**
 * Class Icon
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Icon extends Element
{
    public function __construct( $iconClass = null )
    {
        parent::__construct( 'i' );

        if( isset( $iconClass ) ) {
            $this->attributes->addAttributeClass( $iconClass );
        }
    }
}