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

namespace O2System\Framework\Models\Sql\DataObjects\Result;

// ------------------------------------------------------------------------

use O2System\Database;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplClassInfo;

/**
 * Class Row
 *
 * @package O2System\Framework\Models\Sql\DataObjects
 */
class Row extends SplArrayObject
{
    /**
     * Row::$model
     *
     * @var \O2System\Spl\Info\SplClassInfo
     */
    private $model;

    // ------------------------------------------------------------------------

    /**
     * Row::__construct
     *
     * @param Database\DataObjects\Result\Row|array $row
     * @param Model                                 $model
     */
    public function __construct($row, Model &$model)
    {
        $this->model = new SplClassInfo($model);

        if ( ! models()->has($this->model->getClass())) {
            models()->add($model, $this->model->getClass());
        }

        if ($row instanceof Database\DataObjects\Result\Row) {
            parent::__construct($row->getArrayCopy());
        } elseif (is_array($row)) {
            parent::__construct($row);
        }

        // Append Columns
        if (count($model->appendColumns)) {
            foreach ($model->appendColumns as $appendColumn) {
                $this->offsetSet($appendColumn, $this->offsetGet($appendColumn));
            }
        }

        models($this->model->getClass())->row = $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::offsetGet
     *
     * @param string $offset
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result\Row|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return parent::offsetGet($offset);
        } elseif (null !== ($result = $this->__call($offset))) {
            return $result;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::__call
     *
     * @param string $method
     * @param array  $args
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result\Row|null
     */
    public function __call($method, $args = [])
    {
        $model = models($this->model->getClass());

        if (method_exists($model, $method)) {
            $model->row = $this;

            if (false !== ($result = call_user_func_array([&$model, $method], $args))) {
                $this->offsetSet($method, $result);

                return $result;
            }
        } elseif (strpos($method, 'Url')) {
            $key = str_replace('Url', '', $method);

            if ($key === $model->uploadedImageKey) {
                if (isset($model->uploadedImageFilePath)) {
                    if (is_file($filePath = $model->uploadedImageFilePath . $this->offsetGet($key))) {
                        return images_url($filePath);
                    } elseif (is_file($filePath = PATH_STORAGE . 'images/default/not-found.jpg')) {
                        return images_url($filePath);
                    } elseif (is_file($filePath = PATH_STORAGE . 'images/default/not-found.png')) {
                        return images_url($filePath);
                    }
                }
            } elseif (in_array($key, $model->uploadedImageKeys)) {
                if (isset($model->uploadedImageFilePath)) {
                    if (is_file($filePath = $model->uploadedImageFilePath . $this->offsetGet($key))) {
                        return images_url($filePath);
                    } elseif (is_file($filePath = PATH_STORAGE . 'images/default/not-found.jpg')) {
                        return images_url($filePath);
                    } elseif (is_file($filePath = PATH_STORAGE . 'images/default/not-found.png')) {
                        return images_url($filePath);
                    }
                }
            } elseif ($key === $model->uploadedFileKey) {
                if (isset($model->uploadedFileFilepath)) {
                    return storage_url($model->uploadedFileFilepath . $this->offsetGet($key));
                }
            } elseif (in_array($key, $model->uploadedFileKeys)) {
                if (isset($model->uploadedFileFilepath)) {
                    return storage_url($model->uploadedFileFilepath . $this->offsetGet($key));
                }
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::delete
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete()
    {
        $model = models($this->model->getClass());

        $primaryKey = isset($model->primaryKey) ? $model->primaryKey : 'id';
        $id = $this->offsetGet($primaryKey);

        if (method_exists($model, 'beforeDelete')) {
            $model->beforeDelete();
        }

        if ($model->qb->table($model->table)->limit(1)->delete([$primaryKey => $id])) {
            if (method_exists($model, 'afterDelete')) {
                return $model->afterDelete();
            }

            return true;
        }

        return false;
    }
}