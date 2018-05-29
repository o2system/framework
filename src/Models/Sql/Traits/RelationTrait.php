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

namespace O2System\Framework\Models\Sql\Traits;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Relations;

/**
 * Class RelationTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait RelationTrait
{
    /**
     * Belongs To
     *
     * Belongs To is the inverse of one to one relationship.
     *
     * @param string|Model $referenceModel
     * @param string|null  $foreignKey
     * @param string|null  $primaryKey
     *
     * @return Row|bool
     */
    public function belongsTo($referenceModel, $foreignKey = null, $primaryKey = null)
    {
        return (new Relations\BelongsTo(
            new Relations\Maps\Inverse($this, $referenceModel, $foreignKey, $primaryKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Belongs To Many
     *
     * Belongs To is the inverse of one to many relationship.
     *
     * @param string|Model $relationModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     * @param string|null  $primaryKey
     *
     * @return Row|bool
     */
    public function belongsToMany($relationModel, $pivotTable = null, $foreignKey = null, $primaryKey = null)
    {
        return (new Relations\BelongsToMany(
            new Relations\Maps\Intermediary($this, $relationModel, $pivotTable, $foreignKey, $primaryKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has One
     *
     * Has one is a one to one relationship. The reference model might be associated
     * with one relation model / table.
     *
     * @param string|Model $relationModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     * @param string|null  $primaryKey
     *
     * @return Row|bool
     */
    public function hasOne($relationModel, $foreignKey = null, $primaryKey = null)
    {
        return (new Relations\HasOne(
            new Relations\Maps\Reference($this, $relationModel, $foreignKey, $primaryKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has Many
     *
     * Has Many is a one to many relationship, is used to define relationships where a single
     * reference model owns any amount of others relation model.
     *
     * @param string|Model $relationModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     * @param string|null  $primaryKey
     *
     * @return Result|bool
     */
    public function hasMany($relationModel, $foreignKey = null, $primaryKey = null)
    {
        return (new Relations\HasMany(
            new Relations\Maps\Reference($this, $relationModel, $foreignKey, $primaryKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has Many Through
     *
     * Has Many Through provides a convenient short-cut for accessing distant relations via
     * an intermediate relation model.
     *
     * @param string|Model $relationModel
     * @param string|Model $pivotTable
     * @param string|null  $foreignKey
     * @param string|null  $primaryKey
     *
     * @return Result|bool
     */
    public function hasManyThrough($relationModel, $referenceModel, $pivotForeignKey = null, $relationForeignKey = null)
    {
        return (new Relations\HasManyThrough(
            new Relations\Maps\Through($this, $pivotForeignKey, $relationModel, $relationForeignKey, $referenceModel)
        ))->getResult();
    }
}