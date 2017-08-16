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

namespace O2System\Framework\Models\SQL\DataObjects\Result;

// ------------------------------------------------------------------------

use O2System\Database;
use O2System\Framework\Models\Abstracts\AbstractModel;
use O2System\Framework\Models\SQL\Model;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Row
 *
 * @package O2System\Framework\Models\SQL\DataObjects
 */
class Row extends SplArrayObject
{
    /**
     * Row Model Instance
     *
     * @var Model
     */
    private static $model;

    /**
     * Row::__construct
     *
     * @param Database\DataObjects\Result\Row|array  $row
     * @param Model $model
     */
    public function __construct( $row, Model $model )
    {
        self::$model =& $model;

        if ( $row instanceof Database\DataObjects\Result\Row ) {
            parent::__construct( $row->getArrayCopy() );
        } elseif ( is_array( $row ) ) {
            parent::__construct( $row );
        }

        self::$model->row = $this;
    }

    // ------------------------------------------------------------------------

    public function &__get( $method )
    {
        $returnValue = null;

        if ( method_exists( self::$model, $method ) ) {
            $returnValue = $this->__call( $method );
        } else {
            $returnValue = parent::__get( $method );
        }

        return $returnValue;
    }

    // ------------------------------------------------------------------------

    /**
     * Call Override
     *
     * This method is act as magic method, inspired from Laravel Eloquent ORM
     *
     * @access  public
     *
     * @param   string $method
     * @param   array  $args
     *
     * @return  mixed
     */
    public function __call( $method, $args = [] )
    {
        if ( method_exists( self::$model, $method ) ) {
            self::$model->row = $this;

            return call_user_func_array( [ &self::$model, $method ], $args );
        }

        return null;
    }
}