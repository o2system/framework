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

/**
 * Class TraitRecord
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait RecordTrait
{
    /**
     * Unix Timestamp Flag
     *
     * @access  protected
     * @type    bool
     */
    protected $unixTimestamp = false;

    /**
     * Default Record Status
     *
     * @access  protected
     * @type    string
     */
    protected $recordStatus = 'PUBLISH';

    /**
     * Default Record User
     *
     * @access  protected
     * @type    int
     */
    protected $recordUser = null;

    /**
     * Record Ordering Flag
     *
     * @var bool
     */
    protected $recordOrdering = false;

    protected function setRecordUser($idUser)
    {
        if (is_numeric($idUser)) {
            $this->recordUser = $idUser;
        }

        return $this;
    }

    protected function setRecordStatus($status)
    {
        $status = strtoupper($status);

        if (in_array($status, ['UNPUBLISH', 'PUBLISH', 'DRAFT', 'DELETE', 'ARCHIVE'])) {
            $this->recordStatus = $status;
        }

        return $this;
    }

    protected function insertRecordSets(array &$sets)
    {
        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($sets[ 'record_status' ])) {
            $sets[ 'record_status' ] = $this->recordStatus;
        }

        if (empty($this->primaryKeys)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

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

        $sets[ 'record_update_user' ] = $this->recordUser;

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        } elseif ($this->unixTimestamp) {
            $sets[ 'record_update_timestamp' ] = strtotime($sets[ 'record_update_timestamp' ]);
        }
    }

    protected function updateRecordSets(array &$sets)
    {
        $sets[ 'record_status' ] = $this->recordStatus;
        $sets[ 'record_update_user' ] = $this->recordUser;

        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        }
    }

    /**
     * Process Row Ordering
     *
     * @access  public
     */
    protected function getRecordOrdering($table = null)
    {
        $table = isset($table) ? $table : $this->table;

        return $this->qb->countAllResults($table) + 1;
    }

    public function withRecordStatus($recordStatus)
    {
        $this->qb->where('record_status', strtoupper($recordStatus));

        return $this;
    }

    public function createdBy($recordCreateUser)
    {
        $this->qb->where('record_create_user', $recordCreateUser);

        return $this;
    }

    public function updatedBy($recordUpdateUser)
    {
        $this->qb->where('record_update_user', $recordUpdateUser);

        return $this;
    }
}