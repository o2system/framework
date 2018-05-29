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

use O2System\Framework\Models\Files\Model as FileModel;
use O2System\Framework\Models\NoSql\Model as NoSqlModel;
use O2System\Framework\Models\Sql\Model as SqlModel;
use O2System\Spl\Containers\Datastructures\SplServiceRegistry;
use O2System\Spl\Containers\SplServiceContainer;

/**
 * Class Models
 *
 * @package O2System\Framework
 */
class Models extends SplServiceContainer
{
    public function load($model)
    {
        if (is_string($model)) {
            $service = new SplServiceRegistry($model);
        } elseif ($model instanceof SplServiceRegistry) {
            $service = $model;
        }

        if (isset($service) && $service instanceof SplServiceRegistry) {
            $offset = strtolower($service->getClassName());

            if ($service->isSubclassOf('O2System\Framework\Models\Sql\Model') ||
                $service->isSubclassOf('O2System\Framework\Models\NoSql\Model') ||
                $service->isSubclassOf('O2System\Framework\Models\Files\Model')
            ) {
                models()->attach($offset, $service);
            }
        }
    }

    /**
     * Models::register
     *
     * @param string                                                                                                             $offset
     * @param \O2System\Framework\Models\Sql\Model|\O2System\Framework\Models\NoSql\Model|\O2System\Framework\Models\Files\Model $model
     */
    public function register($offset, $model)
    {
        if ($model instanceof SqlModel OR $model instanceof NoSqlModel OR $model instanceof FileModel) {

            parent::attach($offset, new SplServiceRegistry($model));
        }
    }
}