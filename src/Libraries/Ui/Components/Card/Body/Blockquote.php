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

namespace O2System\Framework\Libraries\Ui\Components\Card\Body;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Link;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;
use O2System\Html\Element;

/**
 * Class Blockquote
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card\Body
 */
class Blockquote extends Element
{
    use ParagraphSetterTrait;

    public $author;
    public $source;

    public function __construct()
    {
        parent::__construct( 'div', 'blockquote' );
        $this->attributes->addAttributeClass( 'card-blockquote' );
    }

    public function setAuthor( $name, $href = null )
    {
        $this->author = new Element( 'small', 'author' );

        if ( isset( $href ) ) {
            $this->author->childNodes->push( new Link( $name, $href ) );
        } else {
            $this->author->textContent->push( $name );
        }

        return $this;
    }

    public function setSource( $name, $href = null )
    {
        $this->source = new Element( 'cite', 'source' );

        if ( isset( $href ) ) {
            $this->source->childNodes->push( new Link( $name, $href ) );
        } else {
            $this->source->textContent->push( $name );
        }

        return $this;
    }

    public function render()
    {
        if ( $this->paragraph instanceof Element ) {
            $this->childNodes->push( $this->paragraph );
        }

        $footer = new Element( 'div', 'footer' );
        $footer->attributes->addAttributeClass( 'blockquote-footer' );

        if ( $this->author instanceof Element ) {
            $footer->childNodes->push( $this->author );

            if ( $this->author->tagName === 'small' && $this->source instanceof Element ) {
                $this->author->childNodes->push( $this->source );
            } elseif ( $this->source instanceof Element ) {
                $footer->childNodes->push( $this->source );
            }
        } elseif ( $this->source instanceof Element ) {
            $footer->childNodes->push( $this->source );
        }

        $this->childNodes->push( $footer );

        if ( $this->hasChildNodes() ) {
            return parent::render();
        }

        return '';
    }
}