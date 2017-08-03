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

use O2System\Framework\Libraries\Ui\Components\Jumbotron\Paragraph;
use O2System\Framework\Libraries\Ui\Traits\Collectors\LinksCollectorTrait;
use O2System\Framework\Libraries\Ui\Traits\Collectors\ParagraphsCollectorTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\PopoverSetterTrait;
use O2System\Html\Element;

/**
 * Class Jumbotron
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Jumbotron extends Element
{
    public function __construct()
    {
        parent::__construct( 'div' );
        $this->attributes->addAttributeClass( 'jumbotron' );
    }

    public function createHeader( $text, $tagName = 'h1', array $attributes = [ 'class' => 'display-3' ] )
    {
        $header = new Element( $tagName, 'header-' . dash( $text ) );
        $header->textContent->push( $text );

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $header->attributes->addAttribute( $name, $value );
            }
        }

        $this->childNodes->push( $header );

        return $this->childNodes->last();
    }

    public function createHorizontalRule( array $attributes = [] )
    {
        $hr = new Element( 'hr' );

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $hr->attributes->addAttribute( $name, $value );
            }
        }

        $this->childNodes->push( $hr );

        return $this->childNodes->last();
    }

    /**
     * @param null  $text
     * @param array $attributes
     *
     * @return Paragraph
     */
    public function createParagraph( $text = null, array $attributes = [] )
    {
        $paragraph = new Paragraph();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $paragraph->attributes->addAttribute( $name, $value );
            }
        }

        if ( $text instanceof Element ) {
            $paragraph->childNodes->push( $text );
        } elseif ( ! is_null( $text ) ) {
            $paragraph->textContent->push( $text );
        }

        $this->childNodes->push( $paragraph );

        return $this->childNodes->last();
    }

    public function fluid()
    {
        $this->attributes->addAttributeClass( 'jumbotron-fluid' );

        return $this;
    }

    public function render()
    {
        if ( $this->attributes->hasAttributeClass( 'jumbotron-fluid' ) ) {

            $output[] = $this->open();

            $container = new Element( 'div', 'container' );
            $container->attributes->addAttributeClass( 'container-fluid' );

            if ( $this->hasChildNodes() ) {
                foreach ( $this->childNodes as $childNode ) {
                    $container->childNodes->push( $childNode );
                }
            }

            $output[] = $container;
            $output[] = $this->close();

            return implode( PHP_EOL, $output );
        }

        return parent::render();
    }
}