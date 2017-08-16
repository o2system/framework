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

namespace O2System\Framework\Models\SQL\Relations\Mappers;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Abstracts\AbstractModel;

/**
 * Class Intermediate
 *
 * @package O2System\Framework\Models\SQL\Relations\Mappers
 */
class Intermediate
{
    /**
     * Relation Model
     *
     * @var AbstractModel
     */
    public $relationModel;

    /**
     * Relation Table
     *
     * @var string
     */
    public $relationTable;

    /**
     * Relation Foreign Key
     *
     * Foreign Key of Reference Table
     *
     * @var string
     */
    public $relationForeignKey;

    /**
     * Reference Model
     *
     * @var AbstractModel
     */
    public $referenceModel;

    /**
     * Reference Table
     *
     * @var string
     */
    public $referenceTable;

    /**
     * Reference Primary Key
     *
     * @var string
     */
    public $referencePrimaryKey;

    // ------------------------------------------------------------------------

    /**
     * IntermediateMapper constructor.
     *
     * @param AbstractModel        $relationModel
     * @param string|AbstractModel $referenceModel
     * @param string|AbstractModel $intermediateModel
     * @param string|null          $relationForeignKey
     * @param string|null          $referencePrimaryKey
     */
    public function __construct(
        AbstractModel $relationModel,
        $referenceModel,
        $intermediateModel,
        $relationForeignKey = null,
        $referencePrimaryKey = null
    ) {
        // Map Relation Model
        $this->relationModel =& $relationModel;

        // Map Relation Table
        $this->relationTable = $relationModel->table;

        // Map Reference Model
        $this->mapReferenceModel( $referenceModel );

        // Map Relation Primary Key
        $this->relationForeignKey = isset( $relationForeignKey ) ? $relationForeignKey
            : $this->mapRelationForeignKey();
    }

    // ------------------------------------------------------------------------

    /**
     * Map Relation Model
     *
     * @param string|AbstractModel $referenceModel
     *
     * @return void
     */
    private function mapReferenceModel( $referenceModel )
    {
        if ( $referenceModel instanceof AbstractModel ) {
            $this->referenceModel = $referenceModel;
            $this->referenceTable = $this->referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->primaryKey;
        } elseif ( class_exists( $referenceModel ) ) {
            $this->referenceModel = new $referenceModel();
            $this->referenceTable = $this->referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->primaryKey;
        } else {
            $this->referenceTable = $referenceModel;
            $this->referencePrimaryKey = 'id';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Map Relation Foreign Key
     *
     * @return string
     */
    private function mapRelationForeignKey()
    {
        $tablePrefixes = [
            't_',
            'tm_',
            'tr_',
            'tb_',
        ];

        return $this->referencePrimaryKey . '_' . str_replace( $tablePrefixes, '', $this->referenceTable );
    }
}