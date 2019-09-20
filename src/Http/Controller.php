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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

/**
 * Class Controller
 *
 * @package O2System\Framework\Http
 */
class Controller extends \O2System\Kernel\Http\Controller
{
    /**
     * Controller::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = false;

    /**
     * Controller::__get
     *
     * Magic method __get.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function &__get($property)
    {
        $get[ $property ] = false;

        // CodeIgniter property aliasing
        if ($property === 'load') {
            $property = 'loader';
        }

        if (services()->has($property)) {
            $get[ $property ] = services()->get($property);
        } elseif (o2system()->__isset($property)) {
            $get[ $property ] = o2system()->__get($property);
        } elseif ($property === 'model') {
            $get[ $property ] = models('controller');
        } elseif ($property === 'services' || $property === 'libraries') {
            $get[ $property ] = services();
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::view
     *
     * @param string $file
     * @param array  $vars
     */
    protected function view($file, array $vars = [])
    {
        view($file, $vars);
    }
}