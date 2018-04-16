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
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * List of Before Process Methods
     *
     * @access  protected
     * @type    array
     */
    protected $beforeModifyProcess = [];

    /**
     * List of After Process Methods
     *
     * @access  protected
     * @type    array
     */
    protected $afterModifyProcess = [];

    // ------------------------------------------------------------------------

    /**
     * Before Process
     *
     * Process row data before insert or update
     *
     * @param $row
     * @param $table
     *
     * @access  protected
     * @return  mixed
     */
    protected function beforeProcess($row, $table)
    {
        if ( ! empty($this->beforeModifyProcess)) {
            foreach ($this->beforeModifyProcess as $processMethod) {
                $row = $this->{$processMethod}($row, $table);
            }
        }

        return $row;
    }

    // ------------------------------------------------------------------------

    /**
     * After Process
     *
     * Runs all after process method actions
     *
     * @access  protected
     * @return  array
     */
    protected function afterProcess()
    {
        $report = [];

        if ( ! empty($this->afterModifyProcess)) {
            foreach ($this->afterModifyProcess as $processMethod) {
                $report[ $processMethod ] = $this->{$processMethod}();
            }
        }

        return $report;
    }
    // ------------------------------------------------------------------------

    /**
     * Insert Data
     *
     * Method to input data as well as equipping the data in accordance with the fields
     * in the destination database table.
     *
     * @access  public
     *
     * @param   array  $sets  Array of Input Data
     * @param   string $table Table Name
     *
     * @return mixed
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function insert(array $sets, $table = null)
    {
        $table = isset($table) ? $table : $this->table;

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->insert($sets)) {
            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    /**
     * Update Data
     *
     * Method to update data as well as equipping the data in accordance with the fields
     * in the destination database table.
     *
     * @access  public
     *
     * @param   string $table Table Name
     * @param   array  $sets  Array of Update Data
     *
     * @return mixed
     */
    public function update(array $sets, $where = [], $table = null)
    {
        $table = isset($table) ? $table : $this->table;
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (empty($where)) {
            if (empty($this->primaryKeys)) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            } else {
                foreach ($this->primaryKeys as $primaryKey) {
                    $where[ $primaryKey ] = $sets[ $primaryKey ];
                }
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->update($sets, $where)) {

            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    public function insertMany(array $sets)
    {
        $table = isset($table) ? $table : $this->table;

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->insertBatch($sets)) {
            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function updateMany(array $sets)
    {
        $table = isset($table) ? $table : $this->table;
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $where = [];
        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $sets[ $primaryKey ];
            $this->qb->where($primaryKey, $sets[ $primaryKey ]);
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->updateBatch($sets, $where)) {

            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * trash
     *
     * @param      $id
     * @param null $table
     *
     * @return array|bool
     */
    public function trash($id, $table = null)
    {
        $table = isset($table) ? $table : $this->table;
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $sets[ 'record_status' ] = 'TRASH';
        $sets[ 'record_delete_timestamp' ] = date('Y-m-d H:i:s');

        if (services()->has('user')) {
            $sets[ 'record_delete_user' ] = services()->user->getAccount()->id;
        }

        $where = [];

        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
            $sets[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
                $sets[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }

            $sets[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->update($sets, $where)) {
            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function trashBy($id, array $where = [], $table = null)
    {
        $this->qb->where($where);

        return $this->trash($id, $table);
    }

    // ------------------------------------------------------------------------

    /**
     * Trash many rows from the database table based on sets of ids.
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function trashMany(array $ids)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->trash($id);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function trashManyBy(array $ids, $where = [], $table = null)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->trashBy($id, $where, $table);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function delete($id, $force = false, $table = null)
    {
        if ((isset($table) AND is_bool($table)) OR ! isset($table)) {
            $table = $this->table;
        }

        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $where = [];
        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            $where[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if ($this->qb->table($table)->delete($where)) {
            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    public function deleteBy($id, $where = [], $force = false, $table = null)
    {
        $this->qb->where($where);

        return $this->delete($id, $force, $table);
    }

    // ------------------------------------------------------------------------

    public function deleteMany(array $ids, $force = false, $table = null)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->delete($id, $force, $table);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function deleteManyBy(array $ids, $where = [], $force = false, $table = null)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->deleteBy($id, $where, $force, $table);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function publish($id, $table = null)
    {
        $table = isset($table) ? $table : $this->table;
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $sets[ 'record_status' ] = 'PUBLISH';
        $sets[ 'record_update_timestamp' ] = date('Y-m-d H:i:s');
        $sets[ 'record_delete_timestamp' ] = null;

        if (services()->has('user')) {
            $sets[ 'record_update_user' ] = services()->user->getAccount()->id;
            $sets[ 'record_delete_user' ] = null;
        }

        $where = [];

        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
            $sets[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
                $sets[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }

            $sets[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess($sets, $table);

        if ($this->qb->table($table)->update($sets, $where)) {
            if (empty($this->afterModifyProcess)) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function publishBy($id, array $where = [], $table = null)
    {
        $this->qb->where($where);

        return $this->publish($id, $table);
    }

    // ------------------------------------------------------------------------

    public function publishMany(array $ids, $force = false, $table = null)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->publish($id, $force, $table);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function publishManyBy(array $ids, $where = [], $table = null)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->publishBy($id, $where, $table);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function restore($id, $table = null)
    {
        return $this->publish($id, $table);
    }

    // ------------------------------------------------------------------------

    public function restoreBy($id, array $where = [], $table = null)
    {
        return $this->publishBy($id, $where, $table);
    }

    // ------------------------------------------------------------------------

    public function restoreMany(array $ids, $force = false, $table = null)
    {
        return $this->publishMany($ids, $force, $table);
    }

    // ------------------------------------------------------------------------

    public function restoreManyBy(array $ids, $where = [], $table = null)
    {
        return $this->publishManyBy($ids, $where, $table);
    }
}