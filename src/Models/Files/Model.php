<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 14/03/18
 * Time: 19.37
 */

namespace O2System\Framework\Models\Files;


use O2System\Filesystem\Files\JsonFile;
use O2System\Filesystem\Files\XmlFile;
use O2System\Framework\Models\Files\Traits\FinderTrait;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

class Model extends AbstractRepository
{
    use FinderTrait;

    public $file;
    public $result;
    public $primaryKey = 'id';

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
}