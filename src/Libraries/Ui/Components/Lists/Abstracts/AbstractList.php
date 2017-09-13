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

namespace O2System\Framework\Libraries\Ui\Components\Lists\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Link;
use O2System\Framework\Libraries\Ui\Components\Lists\Item;
use O2System\Html\Element;

/**
 * Class AbstractList
 *
 * @package O2System\Framework\Libraries\Ui\Abstracts
 */
abstract class AbstractList extends Element
{
    public function createList( $list = null )
    {
        $node = new Item();

        if ( $list instanceof Item ) {
            $node = $list;
        } elseif ( $list instanceof Element ) {
            $node->entity->setEntityName( $list->entity->getEntityName() );
            $node->childNodes->push( $list );
        } else {
            if ( is_numeric( $list ) ) {
                $node->entity->setEntityName( 'list-' . $list );
            }

            if( isset( $list ) ) {
                $node->entity->setEntityName( $list );
                $node->textContent->push( $list );
            }
        }

        $this->pushChildNode( $node );

        return $this->childNodes->last();
    }

    protected function pushChildNode( Element $node )
    {
        if ( $node->hasChildNodes() ) {
            if ( $node->childNodes->first() instanceof Link ) {
                if ( $node->childNodes->first()->getAttributeHref() === current_url() ) {
                    $node->attributes->addAttributeClass( 'active' );
                    $node->childNodes->first()->attributes->addAttributeClass( 'active' );
                }
            }
        }

        $this->childNodes->push( $node );
    }

    public function render()
    {
        $output[] = $this->open();

        if ( $this->hasChildNodes() ) {
            $output[] = implode( PHP_EOL, $this->childNodes->getArrayCopy() );
        }

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}