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

use O2System\Framework\Abstracts\AbstractModel as FrameworkModel;
use O2System\Orm\Abstracts\AbstractModel as OrmModel;
use O2System\Spl\Containers\Datastructures\SplServiceRegistry;
use O2System\Spl\Containers\SplServiceContainer;

/**
 * Class Models
 *
 * @package O2System\Framework
 */
class Models extends SplServiceContainer
{
    /**
     * Models::register
     *
     * @param string                  $offset
     * @param FrameworkModel|OrmModel $model
     */
    public function register( $offset, $model )
    {
        if ( $model instanceof FrameworkModel OR $model instanceof OrmModel ) {

            parent::attach( $offset, new SplServiceRegistry( $model ) );
        }
    }
}