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
 * Class Testimonial
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card\Body
 */
class Testimonial extends Element
{
    use ParagraphSetterTrait;

    public $photo;
    public $author;
    public $jobTitle;
    public $company;

    public function __construct()
    {
        parent::__construct( 'div', 'testimonial' );
        $this->attributes->addAttributeClass( 'card-testimonial' );
    }

    public function setPhoto( $photo )
    {

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

    public function setJobTitle( $position )
    {
        $this->jobTitle = new Element( 'cite', 'source' );
        $this->jobTitle->textContent->push( $position );

        return $this;
    }

    public function setCompany( $company, $href = null )
    {
        $this->company = new Element( 'cite', 'source' );

        if ( isset( $href ) ) {
            $this->company->childNodes->push( new Link( $company, $href ) );
        } else {
            $this->company->textContent->push( $company );
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

            if ( $this->author->tagName === 'small' && $this->jobTitle instanceof Element ) {
                $this->author->childNodes->push( $this->jobTitle );
            } elseif ( $this->jobTitle instanceof Element ) {
                $footer->childNodes->push( $this->jobTitle );
            }
        } elseif ( $this->jobTitle instanceof Element ) {
            $footer->childNodes->push( $this->jobTitle );
        }

        $this->childNodes->push( $footer );

        if ( $this->hasChildNodes() ) {
            return parent::render();
        }

        return '';
    }
}