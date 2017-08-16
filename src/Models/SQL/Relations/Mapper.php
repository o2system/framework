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

namespace O2System\Framework\Models\SQL\Relations;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Abstracts\AbstractModel;

/**
 * Class Mapper
 *
 * @package O2System\Framework\Models\SQL\Relations
 */
class Mapper
{
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
     * @var string|array
     */
    public $relationForeignKey;

    // ------------------------------------------------------------------------

    /**
     * Mapper constructor.
     *
     * @param AbstractModel        $referenceModel
     * @param string|AbstractModel $relationModel
     * @param string|null          $relationForeignKey
     * @param string|null          $referencePrimaryKey
     */
    public function __construct(
        AbstractModel $referenceModel,
        $relationModel,
        $relationForeignKey = null,
        $referencePrimaryKey = null
    ) {
        // Map Reference Model
        $this->referenceModel =& $referenceModel;

        // Map Reference Table
        $this->referenceTable = $referenceModel->table;

        // Map Reference Primary Key
        $this->referencePrimaryKey = isset( $referencePrimaryKey ) ? $referencePrimaryKey : $referenceModel->primaryKey;

        // Map Relation Model
        $this->mapRelationModel( $relationModel );

        // Map Relation Foreign Key
        $this->relationForeignKey = isset( $relationForeignKey ) ? $relationForeignKey
            : $this->mapRelationForeignKey();
    }

    // ------------------------------------------------------------------------

    /**
     * Map Relation Model
     *
     * @param string|AbstractModel $relationModel
     *
     * @return void
     */
    private function mapRelationModel( $relationModel )
    {
        if ( $relationModel instanceof AbstractModel ) {
            $this->relationModel = $relationModel;
            $this->relationTable = $this->relationModel->table;
        } elseif ( class_exists( $relationModel ) ) {
            $this->relationModel = new $relationModel();
            $this->relationTable = $this->relationModel->table;
        } else {
            $this->relationTable = $relationModel;
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