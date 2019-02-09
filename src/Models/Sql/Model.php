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

use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Framework\Models\Sql\Traits\FinderTrait;
use O2System\Framework\Models\Sql\Traits\ModifierTrait;

/**
 * Class Model
 *
 * @package O2System\Framework\Models\Sql
 */
class Model
{
    use FinderTrait;
    use ModifierTrait;

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
     * @throws \ReflectionException
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

        // Get sub models or siblings models
        if ($filename === 'Model' || $filename === modules()->current()->getDirName()) {
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
                if ($filepath === $filePath) {
                    continue;
                }
                $this->validSubModels[ camelcase(pathinfo($filepath, PATHINFO_FILENAME)) ] = $filepath;
            }
        }
    }

    // ------------------------------------------------------------------------

    final public static function __callStatic($method, array $arguments = [])
    {
        $modelClassName = get_called_class();

        if ( ! models()->has($modelClassName)) {
            models()->load($modelClassName, $modelClassName);
        }

        $modelInstance = models()->get($modelClassName);

        if (method_exists($modelInstance, $method)) {
            return $modelInstance->__call($method, $arguments);
        }

        return false;
    }

    final public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        } elseif (method_exists($this->db, $method)) {
            return call_user_func_array([&$this->db, $method], $arguments);
        } elseif (method_exists($this->qb, $method)) {
            return call_user_func_array([&$this->qb, $method], $arguments);
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
            if (services()->has($property)) {
                return services()->get($property);
            } elseif ($this->hasSubModel($property)) {
                return $this->loadSubModel($property);
            } elseif (o2system()->__isset($property)) {
                return o2system()->__get($property);
            } elseif (models()->__isset($property)) {
                return models()->get($property);
            }
        }
    }

    // ------------------------------------------------------------------------

    final protected function hasSubModel($model)
    {
        if (array_key_exists($model, $this->validSubModels)) {
            return (bool)is_file($this->validSubModels[ $model ]);
        }

        return false;
    }

    final protected function loadSubModel($model)
    {
        if ($this->hasSubModel($model)) {
            $classNames = [
                '\\' . get_called_class() . '\\' . ucfirst($model),
                '\\' . get_namespace(get_called_class()) . ucfirst($model),
            ];

            foreach ($classNames as $className) {
                if (class_exists($className)) {
                    $this->{$model} = new $className();
                    break;
                }
            }
        }

        if (property_exists($this, $model)) {
            return $this->{$model};
        }

        return false;
    }

    final protected function getSubModel($model)
    {
        return $this->loadSubModel($model);
    }
}