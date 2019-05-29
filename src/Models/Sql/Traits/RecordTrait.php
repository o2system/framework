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

/**
 * Class TraitRecord
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait RecordTrait
{
    /**
     * RecordTrait::$unixTimestamp
     *
     * @var bool
     */
    public $unixTimestamp = false;

    /**
     * RecordTrait::$recordStatus
     *
     * @var string
     */
    protected $recordStatus = 'PUBLISH';

    /**
     * RecordTrait::$recordUser
     *
     * @var int|null
     */
    protected $recordUser = null;

    /**
     * Record Ordering Flag
     *
     * @var bool
     */
    public $recordOrdering = false;

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::setRecordUser
     *
     * @param int $idUser
     *
     * @return static
     */
    public function setRecordUser($idUser)
    {
        if (is_numeric($idUser)) {
            $this->recordUser = $idUser;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::setRecordStatus
     *
     * @param string $status
     *
     * @return static
     */
    public function setRecordStatus($status)
    {
        $status = strtoupper($status);

        if (in_array($status, ['UNPUBLISH', 'PUBLISH', 'DRAFT', 'DELETED', 'ARCHIVED', 'LOCKED'])) {
            $this->recordStatus = $status;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::insertRecordSets
     *
     * @param array $sets
     */
    protected function insertRecordSets(array &$sets)
    {
        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if(is_null($this->recordUser)) {
            if(globals()->offsetExists('account')) {
                $this->setRecordUser(globals()->account->id);
            }
        }

        if ( ! isset($sets[ 'record_status' ])) {
            $sets[ 'record_status' ] = $this->recordStatus;
        }

        if (empty($this->primaryKeys)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            if (isset($sets[ $primaryKey ])) {
                if (empty($sets[ $primaryKey ])) {
                    unset($sets[ $primaryKey ]);
                }
            }

            if (empty($sets[ $primaryKey ])) {
                if ( ! isset($sets[ 'record_create_user' ])) {
                    $sets[ 'record_create_user' ] = $this->recordUser;
                }

                if ( ! isset($sets[ 'record_create_timestamp' ])) {
                    $sets[ 'record_create_timestamp' ] = $timestamp;
                } elseif ($this->unixTimestamp) {
                    $sets[ 'record_create_timestamp' ] = strtotime($sets[ 'record_create_timestamp' ]);
                }
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                if (empty($sets[ $primaryKey ])) {
                    if ( ! isset($sets[ 'record_create_user' ])) {
                        $sets[ 'record_create_user' ] = $this->recordUser;
                    }

                    if ( ! isset($sets[ 'record_create_timestamp' ])) {
                        $sets[ 'record_create_timestamp' ] = $timestamp;
                    } elseif ($this->unixTimestamp) {
                        $sets[ 'record_create_timestamp' ] = strtotime($sets[ 'record_create_timestamp' ]);
                    }
                }
            }
        }

        if ( ! isset($sets[ 'record_update_user' ])) {
            $sets[ 'record_update_user' ] = $this->recordUser;
        }

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        } elseif ($this->unixTimestamp) {
            $sets[ 'record_update_timestamp' ] = strtotime($sets[ 'record_update_timestamp' ]);
        }

        if ( ! isset($sets[ 'record_ordering' ]) && $this->recordOrdering === true) {
            $sets[ 'record_ordering' ] = $this->getRecordOrdering();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::updateRecordSets
     *
     * @param array $sets
     */
    protected function updateRecordSets(array &$sets)
    {
        if(is_null($this->recordUser)) {
            if(globals()->offsetExists('account')) {
                $this->setRecordUser(globals()->account->id);
            }
        }

        if ( ! isset($sets[ 'record_status' ])) {
            $sets[ 'record_status' ] = $this->recordStatus;
        }

        if ( ! isset($sets[ 'record_update_user' ])) {
            $sets[ 'record_update_user' ] = $this->recordUser;
        }

        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::getRecordOrdering
     *
     * @return int
     */
    public function getRecordOrdering()
    {
        if($this->recordOrdering === true) {
            return $this->qb->countAllResults($this->table) + 1;
        }

        return 0;
    }
}