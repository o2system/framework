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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Meta\Opengraph\Abstracts\AbstractNamespace;

/**
 * Class Profile
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Profile extends AbstractNamespace
{
    public $namespace = 'profile';

    public function setName($name)
    {
        $xName = explode(' ', $name);
        $firstName = $xName[ 0 ];

        array_shift($xName);

        $lastName = implode(' ', $xName);

        $this->setObject('first_name', $firstName);
        $this->setObject('last_name', $lastName);

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setUsername($username)
    {
        $this->setObject('username', $username);

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setGender($gender)
    {
        $gender = strtolower($gender);

        if (in_array($gender, ['male', 'female'])) {
            $this->setObject('gender', $gender);
        }

        return $this;
    }
}