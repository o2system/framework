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

namespace O2System\Framework\Libraries\Ui\Grid;

// ------------------------------------------------------------------------

use O2System\Html\Element;

/**
 * Class Row
 *
 * @package O2System\Framework\Libraries\Ui\Grid
 */
class Row extends Element
{
    protected $columns = 1;
    protected $childNodesEntities = [];

    // ------------------------------------------------------------------------

    public function __construct( $columns = 1 )
    {
        parent::__construct( 'div' );
        $this->attributes->addAttributeClass( 'row' );

        $this->setColumns( $columns );
    }

    public function setColumns( $columns )
    {
        $this->columns = (int) $columns;

        return $this;
    }

    public function hasItem( $index )
    {
        if ( is_string( $index ) and in_array( $index, $this->childNodesEntities ) ) {
            if ( false !== ( $key = array_search( $index, $this->childNodesEntities ) ) ) {
                if ( $this->childNodes->offsetExists( $key ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getColumn( $index )
    {
        if ( is_string( $index ) and in_array( $index, $this->childNodesEntities ) ) {
            if ( false !== ( $key = array_search( $index, $this->childNodesEntities ) ) ) {
                if ( $this->childNodes->offsetExists( $key ) ) {
                    $index = $key;
                }
            }
        }

        return $this->childNodes->offsetGet( $index );
    }

    // ------------------------------------------------------------------------

    public function addColumn( $column )
    {
        if ( $column instanceof Element ) {
            $this->pushColumnChildNodes( $column );
        }

        return $this;
    }

    protected function pushColumnChildNodes( Element $column )
    {
        if ( ! $this->hasItem( $column->entity->getEntityName() ) ) {
            $this->childNodes[] = $column;
            $this->childNodes->last();
            $this->childNodesEntities[ $this->childNodes->key() ] = $column->entity->getEntityName();
        }
    }

    public function render()
    {
        $output[] = $this->open();

        if ( ! empty( $this->content ) ) {
            $output[] = implode( PHP_EOL, $this->content );
        }

        if ( $this->hasChildNodes() ) {

            if ( $this->columns > 2 ) {
                $extraSmallColumn = @round( 12 / ( $this->columns - 2 ) );
                $smallColumn = @round( 12 / ( $this->columns - 1 ) );
            }

            $mediumColumn = round( 12 / ( $this->columns ) );
            $largeColumn = round( 12 / ( $this->columns ) );

            foreach( $this->childNodes as $childNode ) {
                if ( isset( $extraSmallColumn ) ) {
                    $childNode->attributes->addAttributeClass( 'col-xs-' . $extraSmallColumn );
                }

                if ( isset( $smallColumn ) ) {
                    $childNode->attributes->addAttributeClass( 'col-sm-' . $smallColumn );
                }

                $childNode->attributes->addAttributeClass( 'col-md-' . $mediumColumn );
                $childNode->attributes->addAttributeClass( 'col-lg-' . $largeColumn );

                $output[] = $childNode->render() . PHP_EOL;
            }
        }

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }
}