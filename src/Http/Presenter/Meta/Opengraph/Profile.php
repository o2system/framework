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
    /**
     * Profile::$namespace
     *
     * @var string
     */
    public $namespace = 'profile';

    // ------------------------------------------------------------------------

    /**
     * Profile::setName
     *
     * @param string $name
     *
     * @return static
     */
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

    /**
     * Profile::setUsername
     *
     * @param string $username
     *
     * @return static
     */
    public function setUsername($username)
    {
        $this->setObject('username', $username);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Profile::setGender
     *
     * @param string $gender
     *
     * @return static
     */
    public function setGender($gender)
    {
        $gender = strtolower($gender);

        if (in_array($gender, ['male', 'female'])) {
            $this->setObject('gender', $gender);
        }

        return $this;
    }
}