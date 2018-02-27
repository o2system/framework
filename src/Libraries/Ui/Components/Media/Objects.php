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

namespace O2System\Framework\Libraries\Ui\Components\Media;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Lists\Item;
use O2System\Framework\Libraries\Ui\Traits\Setters\HeadingSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ImageSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Objects
 *
 * @package O2System\Framework\Libraries\Ui\Components\Media
 */
class Objects extends Element
{
    use ImageSetterTrait;
    use HeadingSetterTrait;
    use ParagraphSetterTrait;

    public $container;
    public $body;

    public function __construct()
    {
        parent::__construct( 'div' );
        $this->attributes->addAttributeClass( 'media' );

        $this->container = new Element( 'div' );
        $this->container->attributes->addAttributeClass( 'media-left' );

        $this->body = new Element( 'div' );
        $this->body->attributes->addAttributeClass( 'media-body' );
    }

    public function alignMiddle()
    {
        $this->container->attributes->addAttributeClass( 'media-middle' );

        return $this;
    }

    public function alignBottom()
    {
        $this->container->attributes->addAttributeClass( 'media-bottom' );

        return $this;
    }

    public function createNestedObject( $list = null )
    {
        $node = new Objects();
        $node->tagName = 'div';

        if ( $list instanceof Objects ) {
            $node = $list;
        } elseif ( $list instanceof Element ) {
            $node->entity->setEntityName( $list->entity->getEntityName() );
            $node->childNodes->push( $list );
        } else {
            $node->entity->setEntityName( 'media-nested-' . ( $this->childNodes->count() + 1 ) );

            if ( isset( $list ) ) {
                $node->entity->setEntityName( $list );
                $node->textContent->push( $list );
            }
        }

        $this->childNodes->push( $node );

        return $this->childNodes->last();
    }

    public function render()
    {
        $output[] = $this->open();

        if ( $this->image instanceof Element ) {
            $this->image->attributes->addAttributeClass( [ 'media-image', 'd-flex', 'mr-3' ] );
            $this->container->childNodes->push( $this->image );
        }

        if ( $this->paragraph instanceof Element ) {
            $this->body->childNodes->prepend( $this->paragraph );
        }

        if ( $this->hasChildNodes() ) {
            foreach ( $this->childNodes as $childNode ) {
                $this->body->childNodes->push( $childNode );
            }
        }

        if ( $this->heading instanceof Element ) {
            $this->heading->tagName = 'h4';
            $this->heading->attributes->addAttributeClass( [ 'media-heading', 'mt-0' ] );
            $this->body->childNodes->prepend( $this->heading );
        }

        $output[] = $this->container;
        $output[] = $this->body;

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}