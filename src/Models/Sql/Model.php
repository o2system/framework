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

namespace O2System\Framework\Models\Sql;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Framework\Models\Sql\Traits\FinderTrait;

/**
 * Class Model
 *
 * @package O2System\Framework\Models\Sql
 */
class Model
{
    use FinderTrait;

    /**
     * AbstractModel::$db
     *
     * Database connection instance.
     *
     * @var \O2System\Database\Sql\Abstracts\AbstractConnection
     */
    public $db = null;

    /**
     * AbstractModel::$qb
     *
     * Database query builder instance.
     *
     * @var \O2System\Database\Sql\Abstracts\AbstractQueryBuilder
     */
    public $qb = null;

    /**
     * Model Table
     *
     * @access  public
     * @type    string
     */
    public $table = null;

    /**
     * Model Table Columns
     *
     * @access  public
     * @type    array
     */
    public $fields = [];

    /**
     * Model Table Primary Key
     *
     * @access  public
     * @type    string
     */
    public $primaryKey = 'id';

    /**
     * Model Table Primary Keys
     *
     * @access  public
     * @type    array
     */
    public $primaryKeys = [];
    /**
     * Model Result
     *
     * @var \O2System\Framework\Models\Sql\DataObjects\Result
     */
    public $result;
    /**
     * Model Result Row
     *
     * @var \O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public $row;
    /**
     * List of library valid sub models
     *
     * @access  protected
     *
     * @type    array   driver classes list
     */
    protected $validSubModels = [];

    // ------------------------------------------------------------------------

    /**
     * AbstractModel::__construct
     */
    public function __construct()
    {
        // Set database connection
        if (method_exists(database(), 'loadConnection')) {
            if ($this->db = database()->loadConnection('default')) {
                $this->qb = $this->db->getQueryBuilder();
            }
        }

        // Set database table
        if (empty($this->table)) {
            $modelClassName = get_called_class();
            $modelClassName = get_class_name($modelClassName);
            $this->table = underscore($modelClassName);
        }

        // Fetch sub-models
        $this->fetchSubModels();
    }

    /**
     * AbstractModel::fetchSubModels
     *
     * @access  protected
     * @final   this method cannot be overwritten.
     *
     * @return void
     */
    final protected function fetchSubModels()
    {
        $reflection = new \ReflectionClass(get_called_class());

        // Define called model class filepath
        $filePath = $reflection->getFileName();

        // Define filename for used as subdirectory name
        $filename = pathinfo($filePath, PATHINFO_FILENAME);

        // Get model class directory name
        $dirName = dirname($filePath) . DIRECTORY_SEPARATOR;

        if ($filename === 'Model') {
            $subModelsDirName = dirname($dirName) . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR;

            if (is_dir($subModelsDirName)) {
                $subModelPath = $subModelsDirName;
            }
        } elseif (is_dir($subModelsDirName = $dirName . $filename . DIRECTORY_SEPARATOR)) {
            $subModelPath = $subModelsDirName;
        }

        if (isset($subModelPath)) {
            loader()->addNamespace($reflection->name, $subModelPath);

            foreach (glob($subModelPath . '*.php') as $filepath) {
                $this->validSubModels[ camelcase(pathinfo($filepath, PATHINFO_FILENAME)) ] = $filepath;
            }
        }
    }

    // ------------------------------------------------------------------------

    final public static function __callStatic($method, array $arguments = [])
    {
        static $modelInstance;

        if (empty($modelInstance)) {
            $modelClassName = get_called_class();
            $modelInstance = new $modelClassName();
        }

        if (method_exists($modelInstance, $method)) {
            return call_user_func_array([&$modelInstance, $method], $arguments);
        }

        return false;
    }

    final public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function __get($property)
    {
        if ($this->row instanceof Row) {
            if ($this->row->offsetExists($property)) {
                return $this->row->offsetGet($property);
            }
        }

        if (empty($get[ $property ])) {
            if (o2system()->hasService($property)) {
                return o2system()->getService($property);
            } elseif (array_key_exists($property, $this->validSubModels)) {
                return $this->loadSubModel($property);
            } elseif (o2system()->__isset($property)) {
                return o2system()->__get($property);
            } elseif (models()->__isset($property)) {
                return models()->get($property);
            }
        }
    }

    // ------------------------------------------------------------------------

    final protected function loadSubModel($model)
    {
        if (is_file($this->validSubModels[ $model ])) {
            $className = '\\' . get_called_class() . '\\' . ucfirst($model);
            $className = str_replace('\Base\\Model', '\Models', $className);

            if (class_exists($className)) {
                $this->{$model} = new $className();
            }
        }

        return $this->{$model};
    }

    final protected function getSubModel($model)
    {
        return $this->loadSubModel($model);
    }
}