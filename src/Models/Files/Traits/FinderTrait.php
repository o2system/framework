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

namespace O2System\Framework\Models\Files\Traits;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\DataObjects;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Iterators\ArrayIterator;

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
     * @param null $fields
     * @param null $limit
     *
     * @return bool|DataObjects\Result
     */
    public function all($fields = null, $limit = null)
    {
        $result = $this->storage;

        if (isset($limit)) {
            $result = array_slice($this->storage, $limit);
        }

        if (empty($fields)) {
            return $this->result = new ArrayIterator($result);
        } else {
            $this->result = new ArrayIterator();

            foreach ($result as $row) {
                $item = new SplArrayObject();
                foreach ($fields as $field) {
                    if ($row->offsetExists($field)) {
                        $item[ $field ] = $row->offsetGet($field);
                    }
                }

                $this->result[] = $item;
            }

            return $this->result;
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * FinderTrait::page
     *
     * Find record by page.
     *
     * @param int $page
     */
    public function page($fields = null, $page = 1, $entries = 5)
    {
        $chunks = array_chunk($this->storage, $entries);
        $offset = $page - 1;

        if (isset($chunks[ $offset ])) {
            $result = new ArrayIterator($chunks[ $offset ]);

            if (empty($fields)) {
                return $this->result = $result;
            } else {
                foreach ($result as $row) {
                    $item = new SplArrayObject();
                    foreach ($fields as $field) {
                        if ($row->offsetExists($field)) {
                            $item[ $field ] = $row->offsetGet($field);
                        }
                    }

                    $this->result[] = $item;
                }

                return $this->result;
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find
     *
     * Find single or many record base on criteria by specific field
     *
     * @param   string      $criteria Criteria value
     * @param   string|null $field    Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|DataObjects\Result\Row|bool Returns FALSE if failed.
     */
    public function find($criteria, $field = null, $limit = null)
    {
        if (is_array($criteria)) {
            return $this->findIn($criteria, $field);
        }

        $field = isset($field) ? $field : $this->primaryKey;

        $result = new ArrayIterator();

        $counter = 0;
        foreach ($this->storage as $row) {
            if ($row->offsetExists($field)) {
                if ($row->offsetGet($field) === $criteria) {
                    $result[] = $row;
                    $counter++;
                }
            }

            if (isset($limit)) {
                if ($counter == $limit) {
                    break;
                }
            }
        }

        if ($result->count() > 0) {
            $this->result = $result;

            if ($result->count() == 1) {
                return $result->first();
            } else {
                return $this->result;
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find In
     *
     * Find many records within criteria on specific field
     *
     * @param   array  $inCriteria List of criteria
     * @param   string $field      Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findIn(array $inCriteria, $field = null)
    {
        $field = isset($field) ? $field : $this->primaryKey;

        $result = new ArrayIterator();

        $counter = 0;
        foreach ($this->storage as $row) {
            if ($row->offsetExists($field)) {
                if (in_array($row->offsetGet($field), $inCriteria)) {
                    $result[] = $row;
                    $counter++;
                }
            }

            if (isset($limit)) {
                if ($counter == $limit) {
                    break;
                }
            }
        }

        if ($result->count() > 0) {
            $this->result = $result;

            if ($result->count() == 1) {
                return $result->first();
            } else {
                return $this->result;
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find By
     *
     * Find single record based on certain conditions
     *
     * @param   array $conditions List of conditions with criteria
     *
     * @access  protected
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findWhere(array $conditions, $limit = null)
    {
        $result = new ArrayIterator();

        $counter = 0;
        foreach ($this->storage as $row) {
            foreach ($conditions as $field => $criteria) {
                if ($row->offsetGet($field) === $criteria) {
                    $result[] = $row;
                    $counter++;
                }
            }

            if (isset($limit)) {
                if ($counter == $limit) {
                    break;
                }
            }
        }

        if ($result->count() > 0) {
            $this->result = $result;

            if ($result->count() == 1) {
                return $result->first();
            } else {
                return $this->result;
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find In
     *
     * Find many records not within criteria on specific field
     *
     * @param   array  $notInCriteria List of criteria
     * @param   string $field         Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findNotIn(array $notInCriteria, $field = null)
    {
        $field = isset($field) ? $field : $this->primaryKey;

        $result = new ArrayIterator();

        $counter = 0;
        foreach ($this->storage as $row) {
            if ($row->offsetExists($field)) {
                if ( ! in_array($row->offsetGet($field), $notInCriteria)) {
                    $result[] = $row;
                    $counter++;
                }
            }

            if (isset($limit)) {
                if ($counter == $limit) {
                    break;
                }
            }
        }

        if ($result->count() > 0) {
            $this->result = $result;

            if ($result->count() == 1) {
                return $result->first();
            } else {
                return $this->result;
            }
        }

        return false;
    }
}