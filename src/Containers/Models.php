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

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use O2System\Framework\Models\SQL\Model as SQLModel;
use O2System\Framework\Models\NoSQL\Model as NoSQLModel;
use O2System\Spl\Containers\Datastructures\SplServiceRegistry;
use O2System\Spl\Containers\SplServiceContainer;

/**
 * Class Models
 *
 * @package O2System\Framework
 */
class Models extends SplServiceContainer
{
    public function load( $model )
    {
        if ( is_string( $model ) ) {
            $service = new SplServiceRegistry( $model );
        } elseif ( $model instanceof SplServiceRegistry ) {
            $service = $model;
        }

        $offset = strtolower( $service->getClassName() );

        if ( $service->isSubclassOf( 'O2System\Framework\Models\SQL\Model' ) ||
            $service->isSubclassOf( 'O2System\Framework\Models\NoSQL\Model' )
        ) {
            models()->attach( $offset, $service );
        }
    }

    /**
     * Models::register
     *
     * @param string                                                                      $offset
     * @param \O2System\Framework\Models\SQL\Model|\O2System\Framework\Models\NoSQL\Model $model
     */
    public function register( $offset, $model )
    {
        if ( $model instanceof SQLModel OR $model instanceof NoSQLModel ) {

            parent::attach( $offset, new SplServiceRegistry( $model ) );
        }
    }
}