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

use O2System\Framework\Models\Sql\DataObjects;

/**
 * Class FinderTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait FinderTrait
{
    /**
     * FinderTrait::all
     *
     * @param array|string|null $fields
     * @param int|null          $limit
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function all($fields = null, $limit = null)
    {
        if (isset($fields)) {
            $this->qb->select($fields);
        }

        if (isset($limit)) {
            $this->qb->limit($limit);
        }

        if (property_exists($this, 'hierarchical')) {
            $this->qb->orderBy($this->table . '.record_ordering', 'ASC');
            $this->qb->orderBy($this->table . '.record_left', 'ASC');
        } elseif (property_exists($this, 'adjacency')) {
            $this->qb->orderBy($this->table . '.record_ordering', 'ASC');
        }

        if ($result = $this->qb->from($this->table)->get()) {
            if ($result->count() > 0) {
                $this->result = new DataObjects\Result($result, $this);

                return $this->result;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::allWithPaging
     *
     * @param array|string|null $fields
     * @param int|null          $numPerPage
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function allWithPaging($fields = null, $numPerPage = null)
    {
        return $this->withPaging(null, $numPerPage)->all($fields, $numPerPage);
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::withPaging
     *
     * @param int|null $page
     * @param int|null $numPerPage
     *
     * @return bool|\O2System\Framework\Models\Sql\Model
     */
    public function withPaging($page = null, $numPerPage = null)
    {
        $getPage = $this->input->get('page');
        $getLimit = $this->input->get('limit');

        $page = empty($page) ? (empty($getPage) ? 1 : $getPage) : $page;
        $numPerPage = empty($numPerPage) ? (empty($getLimit) ? 10 : $getLimit) : $numPerPage;

        $this->qb->page($page, $numPerPage);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::paginate
     *
     * @param int $numPerPage
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function paginate($numPerPage)
    {
        return $this->withPaging(null, $numPerPage)->all($numPerPage);
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::find
     *
     * @param mixed       $criteria
     * @param string|null $field
     * @param int|null    $limit
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function find($criteria, $field = null, $limit = null)
    {
        if (is_array($criteria)) {
            return $this->findIn($criteria, $field);
        }

        $field = isset($field) ? $field : $this->primaryKey;
        if (strpos($field, '.') === false) {
            $field = $this->table . '.' . $field;
        }

        if ($result = $this->qb
            ->from($this->table)
            ->where($field, $criteria)
            ->get($limit)) {
            if ($result->count() > 0) {
                $this->result = new DataObjects\Result($result, $this);

                if ($this->result->count() == 1) {
                    return $this->result->first();
                }

                return $this->result;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::findWhere
     *
     * @param array    $conditions
     * @param int|null $limit
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function findWhere(array $conditions, $limit = null)
    {
        foreach ($conditions as $field => $criteria) {
            if (strpos($field, '.') === false) {
                $field = $this->table . '.' . $field;
            }
            $this->qb->where($field, $criteria);
        }

        if ($result = $this->qb
            ->from($this->table)
            ->get($limit)) {
            $this->result = new DataObjects\Result($result, $this);

            if ($limit == 1) {
                return $this->result->first();
            }

            return $this->result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::findIn
     *
     * @param array       $inCriteria
     * @param string|null $field
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function findIn(array $inCriteria, $field = null)
    {
        $field = isset($field) ? $field : $this->primaryKey;
        $field = $this->table . '.' . $field;

        if ($result = $this->qb
            ->from($this->table)
            ->whereIn($field, $inCriteria)
            ->get()) {
            $this->result = new DataObjects\Result($result, $this);

            return $this->result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * FinderTrait::findNotIn
     *
     * @param array       $notInCriteria
     * @param string|null $field
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function findNotIn(array $notInCriteria, $field = null)
    {
        $field = isset($field) ? $field : $this->primaryKey;
        if (strpos($field, '.') === false) {
            $field = $this->table . '.' . $field;
        }

        if ($result = $this->qb
            ->from($this->table)
            ->whereNotIn($field, $notInCriteria)
            ->get()) {
            $this->result = new DataObjects\Result($result, $this);

            return $this->result;
        }

        return false;
    }
}