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

namespace O2System\Framework\Models\NoSQL\Traits;

// ------------------------------------------------------------------------

use O2System\Database\Datastructures\Result;
use O2System\Framework\Abstracts\AbstractModel;
use O2System\Framework\Models\Datastructures\Row;
use O2System\Framework\Models\Relations;

/**
 * Class RelationTrait
 *
 * @package O2System\Framework\Models\NoSQL\Traits
 */
trait RelationTrait
{
    /**
     * Has
     *
     * Has provides a convenient short-cut to build one to one relationship result.
     * Only a single query will be executed.
     *
     * @param string|AbstractModel $relationModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return Result
     */
    public function has( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\Has( $relationModel, $foreignKey, $primaryKey ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * With
     *
     * With provides a convenient short-cut to build one to many relationship result.
     * Only a single query will be executed.
     *
     * @param string|AbstractModel|array $relationModel String of table name or AbstractModel
     * @param string|null                $foreignKey
     * @param string|null                $primaryKey
     *
     * @return Result
     */
    public function with( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\With( $relationModel, $foreignKey, $primaryKey ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has One
     *
     * Has one is a one to one relationship. The reference model might be associated
     * with one relation model / table.
     *
     * @param string|AbstractModel $relationModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return Row|bool
     */
    public function hasOne( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\HasOne(
            new Relations\Mapper( $this, $relationModel, $foreignKey, $primaryKey )
        ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Belongs To
     *
     * Belongs To is the inverse of one to one relationship.
     *
     * @param string|AbstractModel $relationModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return Datastructures\Result\Row|bool
     */
    public function belongsTo( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\BelongsTo(
            new Relations\Mappers\Inverse( $this, $relationModel, $foreignKey, $primaryKey )
        ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has Many
     *
     * Has Many is a one to many relationship, is used to define relationships where a single
     * reference model owns any amount of others relation model.
     *
     * @param string|AbstractModel $relationModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return array|bool
     */
    public function hasMany( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\HasMany(
            new Relations\Mapper( $this, $relationModel, $foreignKey, $primaryKey )
        ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Belongs To Many
     *
     * Belongs To is the inverse of one to one relationship.
     *
     * @param string|AbstractModel $relationModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return Datastructures\Row|bool
     */
    public function belongsToMany( $relationModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\BelongsToMany(
            new Relations\Mappers\Inverse( $this, $relationModel, $foreignKey, $primaryKey )
        ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Has Many Through
     *
     * Has Many Through provides a convenient short-cut for accessing distant relations via
     * an intermediate relation model.
     *
     * @param string|AbstractModel $relationModel     String of table name or AbstractModel
     * @param string|AbstractModel $intermediateModel String of table name or AbstractModel
     * @param string|null          $foreignKey
     * @param string|null          $primaryKey
     *
     * @return array|bool
     */
    public function hasManyThrough( $relationModel, $intermediateModel, $foreignKey = null, $primaryKey = null )
    {
        return ( new Relations\HasManyThrough(
            new Relations\Mappers\Intermediate( $this, $relationModel, $intermediateModel, $foreignKey, $primaryKey )
        ) )->getResult();
    }

    // ------------------------------------------------------------------------

    /**
     * Morph To
     *
     * Polymorphic relations allow a model to belong to more than one other model on a single association.
     *
     * @param       $subjectModel
     * @param array $subjectForeignKeys
     * @param null  $referencePrimaryKey
     */
    public function morphTo( $subjectModel, array $subjectForeignKeys = [], $referencePrimaryKey = null )
    {

    }

    // ------------------------------------------------------------------------

    /**
     * Morph To Many
     *
     * Morph To Many is a many to many polymorphic relations.
     * In addition to traditional polymorphic relations, you may also define "many-to-many" polymorphic relations.
     *
     * @param string|AbstractModel $subjectModel
     * @param array                $subjectForeignKeys
     * @param null                 $referencePrimaryKey
     */
    public function morphToMany( $subjectModel, array $subjectForeignKeys = [], $referencePrimaryKey = null )
    {

    }

    // ------------------------------------------------------------------------

    /**
     * Morphed By Many
     *
     * Morphed By Many is the inverse of Morph to Many polymorphic relations.
     *
     * @param string|AbstractModel $subjectModel
     * @param array                $subjectForeignKeys
     * @param null                 $referencePrimaryKey
     */
    public function morphedByMany( $subjectModel, array $subjectForeignKeys = [], $referencePrimaryKey = null )
    {

    }
}