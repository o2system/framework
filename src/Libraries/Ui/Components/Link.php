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

use O2System\Framework\Libraries\Ui\Traits\Setters\PopoverSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TooltipSetterTrait;
use O2System\Html\Element;

/**
 * Class Link
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Link extends Element
{
    use PopoverSetterTrait;
    use TooltipSetterTrait;

    public function __construct( $label = null, $href = null )
    {
        parent::__construct( 'a' );

        if ( isset( $label ) ) {

            if ( $label instanceof Element ) {
                $this->childNodes->prepend( $label );
                $this->entity->setEntityName( $label->entity->getEntityName() );
            } else {
                $this->textContent->prepend( $label );
                $this->entity->setEntityName( $label );
            }
        }

        if ( isset( $href ) ) {
            $this->setAttributeHref( $href );
        }
    }

    // ------------------------------------------------------------------------

    public function setAttributeHref( $href )
    {
        if ( strpos( $href, 'http' ) !== false ) {
            $this->attributes->addAttribute( 'href', $href );
        } elseif ( strpos( $href, 'javascript' ) !== false ) {
            $this->attributes->addAttribute( 'href', $href );
        } elseif ( strpos( $href, '#' ) !== false ) {
            $this->attributes->addAttribute( 'href', $href );
        } elseif ( function_exists( 'base_url' ) ) {
            $this->attributes->addAttribute( 'href', base_url( $href ) );
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function getAttributeHref()
    {
        return $this->attributes->offsetGet( 'href' );
    }

    // ------------------------------------------------------------------------

    public function hasIcon()
    {
        return empty( $this->icon ) ? false : true;
    }

    // ------------------------------------------------------------------------

    public function setIcon( Icon $icon )
    {
        $this->icon = $icon;

        return $this;
    }

    public function active()
    {
        $this->attributes->addAttributeClass( 'active' );

        return $this;
    }

    public function disabled()
    {
        $this->attributes->addAttributeClass( 'disabled' );

        return $this;
    }
}