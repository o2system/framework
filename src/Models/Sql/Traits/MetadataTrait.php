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

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Trait MetadataTrait
 * @package O2System\Framework\Models\Sql\Traits
 */
trait MetadataTrait
{
    /**
     * MetadataTrait::$hasMetadata
     * 
     * @var bool 
     */
    protected $hasMetadata = true;

    // ------------------------------------------------------------------------
    
    /**
     * MetadataTrait::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata()
    {
        if(isset($this->metadataForeignKey)) {
            if($result = $this->hasMany(__CLASS__ . '\\Metadata', $this->metadataForeignKey)) {
                $metadata = new SplArrayObject();

                foreach($result as $row) {
                    $metadata->offsetSet($row->name, $row->content);
                }

                return $metadata;
            }
        }

        return null;
    }
}