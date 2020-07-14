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

use O2System\Session;
use O2System\Spl\DataStructures\SplArrayStorage;

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
     * RecordTrait::insertRecordData
     *
     * @param SplArrayStorage $data
     */
    protected function insertRecordData(SplArrayStorage &$data)
    {
        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if(is_null($this->recordUser)) {
            if(services()->has('accessControl')) {
                if(services('accessControl')->loggedIn()) {
                    $this->setRecordUser(session()->account->id);
                }
            }
        }

        if ( ! isset($data[ 'record_status' ])) {
            $data[ 'record_status' ] = $this->recordStatus;
        }

        if (empty($this->primaryKeys)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            if (isset($data[ $primaryKey ])) {
                if (empty($data[ $primaryKey ])) {
                    unset($data[ $primaryKey ]);
                }
            }

            if (empty($data[ $primaryKey ])) {
                if ( ! isset($data[ 'record_create_user' ])) {
                    $data[ 'record_create_user' ] = $this->recordUser;
                }

                if ( ! isset($data[ 'record_create_timestamp' ])) {
                    $data[ 'record_create_timestamp' ] = $timestamp;
                } elseif ($this->unixTimestamp) {
                    $data[ 'record_create_timestamp' ] = strtotime($data[ 'record_create_timestamp' ]);
                }
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                if (empty($data[ $primaryKey ])) {
                    if ( ! isset($data[ 'record_create_user' ])) {
                        $data[ 'record_create_user' ] = $this->recordUser;
                    }

                    if ( ! isset($data[ 'record_create_timestamp' ])) {
                        $data[ 'record_create_timestamp' ] = $timestamp;
                    } elseif ($this->unixTimestamp) {
                        $data[ 'record_create_timestamp' ] = strtotime($data[ 'record_create_timestamp' ]);
                    }
                }
            }
        }

        if ( ! isset($data[ 'record_update_user' ])) {
            $data[ 'record_update_user' ] = $this->recordUser;
        }

        if ( ! isset($data[ 'record_update_timestamp' ])) {
            $data[ 'record_update_timestamp' ] = $timestamp;
        } elseif ($this->unixTimestamp) {
            $data[ 'record_update_timestamp' ] = strtotime($data[ 'record_update_timestamp' ]);
        }

        if ( ! isset($data[ 'record_ordering' ]) && $this->recordOrdering === true) {
            $data[ 'record_ordering' ] = $this->getRecordOrdering();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * RecordTrait::updateRecordData
     *
     * @param SplArrayStorage $data
     */
    protected function updateRecordData(SplArrayStorage &$data)
    {
        if(is_null($this->recordUser)) {
            if(session() instanceof Session) {
                if(session()->offsetExists('account')) {
                    $this->setRecordUser(session()->account->id);
                }
            }
        }

        if ( ! isset($data[ 'record_status' ])) {
            $data[ 'record_status' ] = $this->recordStatus;
        }

        if ( ! isset($data[ 'record_update_user' ])) {
            $data[ 'record_update_user' ] = $this->recordUser;
        }

        $timestamp = $this->unixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($data[ 'record_update_timestamp' ])) {
            $data[ 'record_update_timestamp' ] = $timestamp;
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

    // ------------------------------------------------------------------------

    public function getNumOfRecords()
    {
        return $this->qb->table($this->table)->countAll();
    }
}