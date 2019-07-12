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

namespace O2System\Framework\Models\Sql;

// ------------------------------------------------------------------------

/**
 * Class Migration
 * @package O2System\Framework\Models\Sql
 */
class Migration
{
    /**
     * Model::$db
     *
     * Database connection instance.
     *
     * @var \O2System\Database\Sql\Abstracts\AbstractConnection
     */
    public $db;

    /**
     * Model::$qb
     *
     * Database query builder instance.
     *
     * @var \O2System\Database\Sql\Abstracts\AbstractQueryBuilder
     */
    public $qb;

    // ------------------------------------------------------------------------

    /**
     * Model::$forge
     *
     * Database forge instance.
     *
     * @var \O2System\Database\Sql\Abstracts\AbstractForge
     */
    public $forge;

    // ------------------------------------------------------------------------

    /**
     * Migration::__construct
     */
    public function __construct()
    {
        // Set database connection
        if (method_exists(database(), 'loadConnection')) {
            if ($this->db = database()->loadConnection('default')) {
                $this->qb = $this->db->getQueryBuilder();
                $this->forge = $this->db->getForge();
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::__call
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        } elseif (method_exists($this->db, $method)) {
            return call_user_func_array([&$this->db, $method], $arguments);
        } elseif (method_exists($this->forge, $method)) {
            return call_user_func_array([&$this->forge, $method], $arguments);
        }

        return false;
    }
}