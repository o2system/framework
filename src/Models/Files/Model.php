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

namespace O2System\Framework\Models\Files;

// ------------------------------------------------------------------------

use O2System\Filesystem\Files\JsonFile;
use O2System\Filesystem\Files\XmlFile;
use O2System\Framework\Models\Files\Traits\FinderTrait;
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Model
 * @package O2System\Framework\Models\Files
 */
class Model extends AbstractRepository
{
    use FinderTrait;

    /**
     * Model::$file
     *
     * @var string
     */
    public $file;

    /**
     * Model::$result
     *
     * @var \O2System\Spl\Iterators\ArrayIterator
     */
    public $result;

    /**
     * Model::$primaryKey
     *
     * @var mixed|string
     */
    public $primaryKey = 'id';

    // ------------------------------------------------------------------------

    /**
     * Model::__construct
     */
    public function __construct()
    {
        if ( ! empty($this->file)) {
            $extension = pathinfo($this->file, PATHINFO_EXTENSION);

            switch ($extension) {
                case 'json':
                    $jsonFile = new JsonFile($this->file);
                    $this->storage = $jsonFile->readFile()->getArrayCopy();
                    break;
                case 'xml':
                    $xmlFile = new XmlFile($this->file);
                    $this->storage = $xmlFile->readFile()->getArrayCopy();
                    break;
            }

            $first = reset($this->storage);
            if ( ! isset($first[ $this->primaryKey ])) {
                $keys = $first->getKeys();
                $this->primaryKey = reset($keys);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Model::get
     *
     * @param string $property
     *
     * @return mixed
     */
    public function get($property)
    {
        if (empty($get[ $property ])) {
            if (services()->has($property)) {
                return services()->get($property);
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

    /**
     * Model::loadSubModel
     *
     * @param string $model
     *
     * @return \O2System\Framework\Models\Files\Model
     */
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

    // ------------------------------------------------------------------------

    /**
     * Model::getSubModel
     *
     * @param string $model
     *
     * @return \O2System\Framework\Models\Files\Model
     */
    final protected function getSubModel($model)
    {
        return $this->loadSubModel($model);
    }
}