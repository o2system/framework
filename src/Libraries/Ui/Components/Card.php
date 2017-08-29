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

use O2System\Framework\Libraries\Ui\Components\Card\Body;
use O2System\Framework\Libraries\Ui\Components\Card\Footer;
use O2System\Framework\Libraries\Ui\Components\Card\Header;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TypographySetterTrait;
use O2System\Html\Element;

/**
 * Class Card
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Card extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use TypographySetterTrait;

    public $header;
    public $image;
    public $footer;

    public function __construct( $contextualClass = self::DEFAULT_CONTEXT, $inverse = false )
    {
        parent::__construct( 'div', 'card' );
        $this->attributes->addAttributeClass( 'card' );

        if ( $inverse ) {
            $this->setContextualClassPrefix( 'card' );
        } else {
            $this->setContextualClassPrefix( 'card-outline' );
        }

        $this->setContextualClassSuffix( $contextualClass );

        $this->header = new Header();
        $this->block = new Body();
        $this->footer = new Footer();
    }

    /**
     * @param string      $src
     * @param string|null $alt
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Image
     */
    public function createImage( $src, $alt = null )
    {
        $this->childNodes->push( new \O2System\Framework\Libraries\Ui\Components\Card\Image( $src, $alt ) );

        return $this->image = $this->childNodes->last();
    }

    /**
     * @param string      $src
     * @param string|null $alt
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Image
     */
    public function createCarousel( $id = null )
    {
        $this->childNodes->push( new \O2System\Framework\Libraries\Ui\Components\Card\Carousel( $id ) );

        return $this->image = $this->childNodes->last();
    }

    /**
     * @return \O2System\Framework\Libraries\Ui\Components\Lists\Group
     */
    public function createListGroup()
    {
        $this->childNodes->push( new Lists\Group() );

        return $this->childNodes->last();
    }

    /**
     * @return Body
     */
    public function createBody()
    {
        $this->childNodes->push( new Body() );

        return $this->childNodes->last();
    }

    public function render()
    {
        $output[] = $this->open();

        if ( $this->header->hasTextContent() || $this->header->hasChildNodes() ) {
            $output[] = $this->header;
        } elseif ( $this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Image ||
            $this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Carousel
        ) {
            $this->image->attributes->addAttributeClass( 'card-img-top' );
        }

        if ( $this->hasChildNodes() ) {
            $output[] = implode( PHP_EOL, $this->childNodes->getArrayCopy() );
        }

        if ( $this->footer->hasTextContent() || $this->footer->hasChildNodes() ) {
            $output[] = $this->footer;
        }

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}