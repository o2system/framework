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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

use O2System\Html\Element;

/**
 * Trait HeadingSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait HeadingSetterTrait
{
    public $heading;

    public function setHeading( $text, $tagName = 'h3' )
    {
        $this->heading = new Element( $tagName );
        $this->heading->entity->setEntityName( $text );
        $this->heading->textContent->push( $text );

        return $this;
    }
}