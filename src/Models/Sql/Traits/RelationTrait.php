<?php
/**
 * This file is part of the O2System Framework package.
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
     * RelationTrait::belongsTo
     *
     * Belongs To is the inverse of one to one relationship.
     *
     * @param string|Model $referenceModel
     * @param string|null  $foreignKey
     *
     * @return Row|bool
     */
    protected function belongsTo($referenceModel, $foreignKey = null)
    {
        return (new Relations\BelongsTo(
            new Relations\Maps\Inverse($this, $referenceModel, $foreignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToThrough
     *
     * @param string|Model $referenceModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryCurrentForeignKey
     * @param string|null  $intermediaryReferenceForeignKey
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    protected function belongsToThrough(
        $referenceModel,
        $intermediaryModel,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {
        return (new Relations\BelongsToThrough(
            new Relations\Maps\Intermediary($this, $referenceModel, $intermediaryModel, $intermediaryCurrentForeignKey,
                $intermediaryReferenceForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToMany
     *
     * Belongs To is the inverse of one to many relationship.
     *
     * @param string|Model $referenceModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     *
     * @return Row|bool
     */
    protected function belongsToMany($referenceModel, $foreignKey = null)
    {
        return (new Relations\BelongsToMany(
            new Relations\Maps\Inverse($this, $referenceModel, $foreignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToManyThrough
     *
     * @param string|Model $referenceModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryCurrentForeignKey
     * @param string|null  $intermediaryReferenceForeignKey
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    protected function belongsToManyThrough(
        $referenceModel,
        $intermediaryModel,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {
        return (new Relations\BelongsToManyThrough(
            new Relations\Maps\Intermediary($this, $referenceModel, $intermediaryModel, $intermediaryCurrentForeignKey,
                $intermediaryReferenceForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasOne
     *
     * Has one is a one to one relationship. The reference model might be associated
     * with one relation model / table.
     *
     * @param string|Model $referenceModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     *
     * @return Row|bool
     */
    protected function hasOne($referenceModel, $foreignKey = null)
    {
        return (new Relations\HasOne(
            new Relations\Maps\Reference($this, $referenceModel, $foreignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasOneThrough
     *
     * @param string|Model $referenceModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryCurrentForeignKey
     * @param string|null  $intermediaryReferenceForeignKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    protected function hasOneThrough(
        $referenceModel,
        $intermediaryModel,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {
        return (new Relations\HasOneThrough(
            new Relations\Maps\Intermediary($this, $referenceModel, $intermediaryModel, $intermediaryCurrentForeignKey,
                $intermediaryReferenceForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasMany
     *
     * Has Many is a one to many relationship, is used to define relationships where a single
     * reference model owns any amount of others relation model.
     *
     * @param string|Model $referenceModel String of table name or AbstractModel
     * @param string|null  $foreignKey
     *
     * @return Result|bool
     */
    protected function hasMany($referenceModel, $foreignKey = null)
    {
        return (new Relations\HasMany(
            new Relations\Maps\Reference($this, $referenceModel, $foreignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasManyThrough
     *
     * @param string|Model $referenceModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryCurrentForeignKey
     * @param string|null  $intermediaryReferenceForeignKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    protected function hasManyThrough(
        $referenceModel,
        $intermediaryModel,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {
        return (new Relations\HasManyThrough(
            new Relations\Maps\Intermediary($this, $referenceModel, $intermediaryModel, $intermediaryCurrentForeignKey,
                $intermediaryReferenceForeignKey)
        ))->getResult();
    }
}