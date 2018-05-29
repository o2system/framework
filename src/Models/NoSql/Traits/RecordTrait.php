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

namespace O2System\Framework\Models\NoSql\Traits;

// ------------------------------------------------------------------------

/**
 * Class TraitRecord
 *
 * @package O2System\Framework\Models\NoSql\Traits
 */
trait RecordTrait
{
    /**
     * Unix Timestamp Flag
     *
     * @access  protected
     * @type    bool
     */
    protected $isUnixTimestamp = false;

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
        if (is_null($this->recordUser) and function_exists('globals')) {
            if ($account = globals()->offsetGet('account')) {
                $this->recordUser = isset($account->id_user_account)
                    ? $account->id_user_account
                    : $account->id;
            }
        }

        $timestamp = $this->isUnixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($sets[ 'record_status' ])) {
            $sets[ 'record_status' ] = $this->recordStatus;
        }

        if (empty($this->primary_keys)) {
            $primary_key = isset($this->primary_key) ? $this->primary_key : 'id';

            if (empty($sets[ $primary_key ])) {
                if ( ! isset($sets[ 'record_create_user' ])) {
                    $sets[ 'record_create_user' ] = $this->recordUser;
                }

                if ( ! isset($sets[ 'record_create_timestamp' ])) {
                    $sets[ 'record_create_timestamp' ] = $timestamp;
                }
            }
        } else {
            foreach ($this->primary_keys as $primary_key) {
                if (empty($sets[ $primary_key ])) {
                    if ( ! isset($sets[ 'record_create_user' ])) {
                        $sets[ 'record_create_user' ] = $this->recordUser;
                    }

                    if ( ! isset($sets[ 'record_create_timestamp' ])) {
                        $sets[ 'record_create_timestamp' ] = $timestamp;
                    }
                }
            }
        }

        $sets[ 'record_update_user' ] = $this->recordUser;

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        }
    }

    protected function updateRecordSets(array &$sets)
    {
        $sets[ 'record_status' ] = $this->recordStatus;
        $sets[ 'record_update_user' ] = $this->recordUser;

        $timestamp = $this->isUnixTimestamp === true ? strtotime(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        if ( ! isset($sets[ 'record_update_timestamp' ])) {
            $sets[ 'record_update_timestamp' ] = $timestamp;
        }

        if ($this->recordStatus === 'PUBLISH') {
            $sets[ 'record_delete_timestamp' ] = null;
            $sets[ 'record_delete_user' ] = null;
        }
    }
}