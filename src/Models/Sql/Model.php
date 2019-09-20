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
use O2System\Framework\Models\Sql\Traits\RecordTrait;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Model
 *
 * @package O2System\Framework\Models\Sql
 */
class Model
{
    use FinderTrait;
    use ModifierTrait;
    use RecordTrait;
    use RelationTrait;

    /**
     * Model::$group
     *
     * Database connection group.
     *
     * @var string
     */
    public $group = 'default';

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

    /**
     * Model::$table
     *
     * Database table name
     *
     * @var string
     */
    public $table = null;

    /**
     * Model::$columns
     *
     * Database table columns.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Model::$fillableColumns
     *
     * Database table fillable columns name.
     *
     * @var array
     */
    public $fillableColumns = [];

    /**
     * Model::$hideColumns
     *
     * Database table hide columns name.
     *
     * @var array
     */
    public $hideColumns = [];

    /**
     * Model::$visibleColumns
     *
     * Database table visible columns name.
     *
     * @var array
     */
    public $visibleColumns = [];

    /**
     * Model::$appendColumns
     *
     * Database table append columns name.
     *
     * @var array
     */
    public $appendColumns = [];

    /**
     * Model::$primaryKey
     *
     * Database table primary key field name.
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Model::$primaryKeys
     *
     * Database table primary key columns name.
     *
     * @var array
     */
    public $primaryKeys = [];

    /**
     * Model::$uploadedImageFilePath
     *
     * Storage uploaded image filePath.
     */
    public $uploadedImageFilePath = null;

    /**
     * Model::$uploadedImageKey
     *
     * Database table uploaded image key field name.
     *
     * @var string
     */
    public $uploadedImageKey = null;

    /**
     * Model::$uploadedImageKeys
     *
     * Database table uploaded image key columns name.
     *
     * @var array
     */
    public $uploadedImageKeys = [];

    /**
     * Model::$uploadedFileFilepath
     *
     * Storage uploaded file filePath.
     */
    public $uploadedFileFilepath = null;

    /**
     * Model::$uploadedFileKey
     *
     * Database table uploaded file key field name.
     *
     * @var string
     */
    public $uploadedFileKey = null;

    /**
     * Model::$uploadedFileKeys
     *
     * Database table uploaded file key columns name.
     *
     * @var array
     */
    public $uploadedFileKeys = [];

    /**
     * Model::$result
     *
     * Model Result
     *
     * @var \O2System\Framework\Models\Sql\DataObjects\Result
     */
    public $result;

    /**
     * Model::$row
     *
     * Model Result Row
     *
     * @var \O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public $row;

    /**
     * Result::$rebuildRowCallback
     *
     * @var \Closure
     */
    protected $rebuildRowCallback;

    /**
     * Model::$validSubModels
     *
     * List of library valid sub models
     *
     * @var array
     */
    protected $validSubModels = [];

    // ------------------------------------------------------------------------

    /**
     * Model::__construct
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        // Set database connection
        if (method_exists(database(), 'loadConnection')) {
            if ($this->db = database()->loadConnection($this->group)) {
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

    // ------------------------------------------------------------------------

    /**
     * Model::fetchSubModels
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
        if ($filename === 'Model' || $filename === modules()->top()->getDirName()) {
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

    /**
     * Model::__callStatic
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return bool|mixed
     */
    final public static function __callStatic($method, array $arguments = [])
    {
        $modelClassName = get_called_class();

        if ( ! models()->has($modelClassName)) {
            models()->add(new $modelClassName(), $modelClassName);
        }

        if (false !== ($modelInstance = models($modelClassName))) {
            if (method_exists($modelInstance, $method)) {
                return call_user_func_array([&$modelInstance, $method], $arguments);
            } elseif (method_exists($modelInstance->db, $method)) {
                return call_user_func_array([&$modelInstance->db, $method], $arguments);
            } elseif (method_exists($modelInstance->qb, $method)) {
                return call_user_func_array([&$modelInstance->qb, $method], $arguments);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Model::__call
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return bool|mixed
     */
    final public function __call($method, array $arguments = [])
    {
        return static::__callStatic($method, $arguments);
    }

    // ------------------------------------------------------------------------

    /**
     * Model::__get
     *
     * @param string $property
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
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

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Model::hasSubModel
     *
     * @param string $model
     *
     * @return bool
     */
    final protected function hasSubModel($model)
    {
        if (array_key_exists($model, $this->validSubModels)) {
            return (bool)is_file($this->validSubModels[ $model ]);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Model::loadSubModel
     *
     * @param string $model
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function loadSubModel($model)
    {
        if ($this->hasSubModel($model)) {
            $classNames = [
                '\\' . get_called_class() . '\\' . ucfirst($model),
                '\\' . get_namespace(get_called_class()) . ucfirst($model),
            ];

            foreach ($classNames as $className) {
                if (class_exists($className)) {
                    $this->{$model} = models($className);
                    break;
                }
            }
        }

        if (property_exists($this, $model)) {
            return $this->{$model};
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Model::getSubModel
     *
     * @param string $model
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    final protected function getSubModel($model)
    {
        return $this->loadSubModel($model);
    }

    // ------------------------------------------------------------------------

    /**
     * Model::setRebuildRowCallback
     *
     * @param \Closure $callback
     */
    final public function rebuildRowCallback(\Closure $callback)
    {
        if(empty($this->rebuildRowCallback)) {
            $this->rebuildRowCallback = $callback;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Model::isCallableRebuildRowCallback
     *
     * @return void
     */
    final public function resetRebuildRowCallback()
    {
        $this->rebuildRowCallback = null;
    }

    // ------------------------------------------------------------------------

    /**
     * Model::isCallableRebuildRowCallback
     *
     * @return bool
     */
    final public function isCallableRebuildRowCallback()
    {
        return is_callable($this->rebuildRowCallback);
    }

    // ------------------------------------------------------------------------

    /**
     * Model::rebuildRow
     *
     * @param Row $row
     *
     * @return void
     */
    public function rebuildRow($row)
    {
        if(is_callable($this->rebuildRowCallback)) {
            call_user_func($this->rebuildRowCallback, $row);
        }
    }
}