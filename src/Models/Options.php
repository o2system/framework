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

namespace O2System\Framework\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Files\Model;

/**
 * Class Options
 * @package O2System\Framework\Models
 */
class Options extends Model
{
    public function __construct()
    {
        parent::__construct();
        language()->loadFile('options');
    }

    public function religions()
    {
        $religions = [];

        foreach(['UNDEFINED','HINDU','BUDDHA','MOSLEM','CHRISTIAN','CATHOLIC'] as $religion) {
            $religions[$religion] = language()->getLine($religion);
        }

        return $religions;
    }
}