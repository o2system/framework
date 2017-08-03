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

use O2System\Framework\Libraries\Ui\Components\Form\Group;
use O2System\Html\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Form
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Form extends Element
{
    public function __construct()
    {
        parent::__construct( 'form' );
        $this->attributes->addAttribute( 'role', 'form' );
    }

    public function inline()
    {
        $this->attributes->addAttributeClass( 'form-inline' );

        return $this;
    }

    public function horizontal()
    {
        $this->attributes->addAttributeClass( 'form-horizontal' );

        return $this;
    }

    /**
     * @return Group
     */
    public function createGroup()
    {
        $this->childNodes->push( new Group() );

        return $this->childNodes->last();
    }
}