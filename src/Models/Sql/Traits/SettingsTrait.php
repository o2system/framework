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

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Settings;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Trait SettingsTrait
 * @package O2System\Framework\Models\Sql\Traits
 */
trait SettingsTrait
{
    /**
     * SettingsTrait::$hasSettings
     * 
     * @var bool 
     */
    protected $hasSettings = true;

    // ------------------------------------------------------------------------

    /**
     * SettingsTrait::settings
     *
     * @return array|bool|\O2System\Database\DataObjects\Result
     */
    public function settings()
    {
        $metadata = new SplArrayObject();

        if($result = $this->morphMany(models(Settings::class), 'ownership')) {
            foreach($result as $row) {
                $metadata->offsetSet($row->key, $row->value);
            }
        }

        return $metadata;
    }
}