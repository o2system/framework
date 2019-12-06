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
     * @param string|Model $associateModel
     * @param string|null  $associateForeignKey
     *
     * @return Row|bool
     */
    final protected function belongsTo($associateModel, $associateForeignKey = null)
    {
        return (new Relations\BelongsTo(
            new Relations\Maps\Inverse($this, $associateModel, $associateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $intermediaryAssociateForeignKey
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function belongsToThrough(
        $associateModel,
        $intermediaryModel,
        $intermediaryObjectForeignKey = null,
        $intermediaryAssociateForeignKey = null
    ) {
        return (new Relations\BelongsToThrough(
            (new Relations\Maps\Inverse($this, $associateModel))
                ->setIntermediary($intermediaryModel, $intermediaryObjectForeignKey, $intermediaryAssociateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToMany
     *
     * Belongs To is the inverse of one to many relationship.
     *
     * @param string|Model $associateModel String of table name or AbstractModel
     * @param string|null  $associateForeignKey
     *
     * @return Row|bool
     */
    final protected function belongsToMany($associateModel, $associateForeignKey = null)
    {
        return (new Relations\BelongsToMany(
            new Relations\Maps\Inverse($this, $associateModel, $associateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::belongsToManyThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $intermediaryAssociateForeignKey
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function belongsToManyThrough(
        $associateModel,
        $intermediaryModel,
        $intermediaryObjectForeignKey = null,
        $intermediaryAssociateForeignKey = null
    ) {
        return (new Relations\BelongsToManyThrough(
            (new Relations\Maps\Inverse($this, $associateModel))
                ->setIntermediary($intermediaryModel, $intermediaryObjectForeignKey, $intermediaryAssociateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasOne
     *
     * Has one is a one to one relationship. The reference model might be referenced
     * with one relation model / table.
     *
     * @param string|Model $associateModel String of table name or AbstractModel
     * @param string|null  $associateForeignKey
     *
     * @return Row|bool
     */
    final protected function hasOne($associateModel, $associateForeignKey = null)
    {
        return (new Relations\HasOne(
            new Relations\Maps\Associate($this, $associateModel, $associateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasOneThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $intermediaryAssociateForeignKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function hasOneThrough(
        $associateModel,
        $intermediaryModel,
        $intermediaryObjectForeignKey = null,
        $intermediaryAssociateForeignKey = null
    ) {
        return (new Relations\HasOneThrough(
            (new Relations\Maps\Associate($this, $associateModel))
                ->setIntermediary($intermediaryModel, $intermediaryObjectForeignKey, $intermediaryAssociateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasMany
     *
     * Has Many is a one to many relationship, is used to define relationships where a single
     * reference model owns any amount of others relation model.
     *
     * @param string|Model $associateModel String of table name or AbstractModel
     * @param string|null  $associateForeignKey
     *
     * @return Result|bool
     */
    final protected function hasMany($associateModel, $associateForeignKey = null)
    {
        return (new Relations\HasMany(
            new Relations\Maps\Associate($this, $associateModel, $associateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::hasManyThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $intermediaryAssociateForeignKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    final protected function hasManyThrough(
        $associateModel,
        $intermediaryModel,
        $intermediaryObjectForeignKey = null,
        $intermediaryAssociateForeignKey = null
    ) {
        return (new Relations\HasManyThrough(
            (new Relations\Maps\Associate($this, $associateModel))
                ->setIntermediary($intermediaryModel, $intermediaryObjectForeignKey, $intermediaryAssociateForeignKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphTo
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function morphTo()
    {
        if (empty($morphKey)) {
            $trace = debug_backtrace();

            if (isset($trace[ 1 ][ 'function' ])) {
                $morphKey = $trace[ 1 ][ 'function' ];
            }

            unset($trace);
        }

        if(class_exists($this->row->{$morphKey . '_model'})) {
            return models($this->row->{$morphKey . '_model'})->find($this->row->{$morphKey . '_id'});
        }
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphOne
     *
     * @param string|Model $associateModel
     * @param string       $morphKey
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final public function morphOne($associateModel, $morphKey)
    {
        return (new Relations\MorphOne(
            new Relations\Maps\Polymorphic($this, $associateModel, $morphKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphOneThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $morphKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    final protected function morphOneThrough(
        $associateModel,
        $intermediaryModel,
        $morphKey = null
    ) {
        return (new Relations\MorphOneThrough(
            (new Relations\Maps\Polymorphic($this, $associateModel, $morphKey))
                ->setIntermediary($intermediaryModel)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphMany
     *
     * @param string|Model $associateModel
     * @param string       $morphKey
     */
    final protected function morphMany($associateModel, $morphKey)
    {
        return (new Relations\MorphMany(
            new Relations\Maps\Polymorphic($this, $associateModel, $morphKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphMany
     *
     * @param string|Model  $associateModel
     * @param string        $morphKey
     */
    final protected function morphToMany($associateModel, $morphKey)
    {
        return (new Relations\MorphToMany(
            new Relations\Maps\Polymorphic($this, $associateModel, $morphKey)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphToManyThrough
     *
     * @param string|Model $associateModel
     * @param string|Model $intermediaryModel
     * @param string|null  $intermediaryObjectForeignKey
     * @param string|null  $morphKey
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    final protected function morphToManyThrough(
        $associateModel,
        $intermediaryModel,
        $morphKey
    ) {
        return (new Relations\MorphToManyThrough(
            (new Relations\Maps\Polymorphic($this, $associateModel, $morphKey))
                ->setIntermediary($intermediaryModel)
        ))->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * RelationTrait::morphedByMany
     *
     * @param string|Model $associateModel
     * @param string       $morphKey
     */
    final protected function morphedByMany($associateModel, $morphKey)
    {
        return (new Relations\MorphByMany(
            new Relations\Maps\Polymorphic($this, $associateModel, $morphKey)
        ))->getResult();
    }
}
