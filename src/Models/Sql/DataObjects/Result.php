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

namespace O2System\Framework\Models\Sql\DataObjects;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Result
 *
 * @package O2System\Database\Datastructures
 */
class Result extends ArrayIterator
{
    /**
     * Result::__construct
     *
     * @param array $rows
     */
    public function __construct( \O2System\Database\DataObjects\Result $result, Model &$model )
    {
        if ( $result->count() > 0 ) {
            $key = empty( $model->foreignKey ) ? $model->primaryKey : $model->foreignKey;

            $ormResult = [];

            foreach ( $result as $row ) {
                $ormResult[ $row[ $key ] ] = new Result\Row( $row, $model );
            }

            parent::__construct( $ormResult );
        }
    }

    // ------------------------------------------------------------------------
}