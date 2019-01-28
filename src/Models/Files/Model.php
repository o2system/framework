<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Models\Files;

// ------------------------------------------------------------------------

use O2System\Filesystem\Files\JsonFile;
use O2System\Filesystem\Files\XmlFile;
use O2System\Framework\Models\Files\Traits\FinderTrait;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Model
 * @package O2System\Framework\Models\Files
 */
class Model extends AbstractRepository
{
    use FinderTrait;

    public $file;
    public $result;
    public $primaryKey = 'id';

    // ------------------------------------------------------------------------

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

    final protected function getSubModel($model)
    {
        return $this->loadSubModel($model);
    }
}