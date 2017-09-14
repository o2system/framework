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

namespace O2System\Framework\Models\Sql\DataObjects\Result;

// ------------------------------------------------------------------------

use O2System\Database;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Row
 *
 * @package O2System\Framework\Models\Sql\DataObjects
 */
class Row extends SplArrayObject
{
    /**
     * Row Model Instance
     *
     * @var Model
     */
    private $model;

    /**
     * Row::__construct
     *
     * @param Database\DataObjects\Result\Row|array  $row
     * @param Model $model
     */
    public function __construct( $row, Model &$model )
    {
        $this->model =& $model;

        if ( $row instanceof Database\DataObjects\Result\Row ) {
            parent::__construct( $row->getArrayCopy() );
        } elseif ( is_array( $row ) ) {
            parent::__construct( $row );
        }

        $this->model->row = $this;
    }

    // ------------------------------------------------------------------------

    public function offsetGet( $offset )
    {
        if( method_exists( $this->model, $offset ) ) {
            return $this->__call( $offset );
        }

        return parent::offsetGet( $offset );
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
        if ( method_exists( $this->model, $method ) ) {
            $this->model->row = $this;
            return call_user_func_array( [ &$this->model, $method ], $args );
        }

        return null;
    }
}