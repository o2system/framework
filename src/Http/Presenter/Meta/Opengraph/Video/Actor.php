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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph\Video;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Meta\Opengraph\Profile;

/**
 * Class Actor
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph\Video
 */
class Actor extends Profile
{
    public $namespace = 'video:actor';

    // ------------------------------------------------------------------------

    /**
     * Actor::setRole
     *
     * @param $role
     */
    public function setRole($role)
    {
        $this->setObject('role', $role);
    }
}