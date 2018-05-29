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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

/**
 * Class Presenter
 *
 * @package O2System\Framework\Http
 */
class Presenter extends AbstractRepository
{
    use ConfigCollectorTrait;

    /**
     * Presenter::__construct
     */
    public function __construct()
    {
        $this->store('meta', new Presenter\Meta());
        $this->store('assets', new Presenter\Assets());
        $this->store('partials', new Presenter\Partials());
        $this->store('widgets', new Presenter\Widgets());
        $this->store('theme', new Presenter\Theme());
    }

    public function store($offset, $value, $replace = false)
    {
        if ($value instanceof \Closure) {
            parent::store($offset, call_user_func($value, $this));
        } else {
            parent::store($offset, $value);
        }
    }

    public function initialize()
    {
        if (false !== ($config = config()->loadFile('presenter', true))) {
            $this->setConfig($config);

            // autoload presenter assets
            if ($config->offsetExists('assets')) {
                $this->assets->autoload($config->assets[ 'autoload' ]);
            }

            // autoload presenter theme
            if ($config->offsetExists('theme')) {
                $this->theme->set($config->offsetGet('theme'));
            }
        }

        return $this;
    }

    public function getArrayCopy()
    {
        $storage = $this->storage;

        // Add Services
        $storage[ 'config' ] = config();
        $storage[ 'language' ] = language();
        $storage[ 'session' ] = session();
        $storage[ 'presenter' ] = presenter();
        $storage[ 'input' ] = input();

        // Add Container
        $storage[ 'globals' ] = globals();

        return $storage;
    }

    public function get($property)
    {
        if (o2system()->hasService($property)) {
            return o2system()->getService($property);
        } elseif (o2system()->__isset($property)) {
            return o2system()->__get($property);
        } elseif (property_exists($this, $property)) {
            return $this->{$property};
        }

        return parent::get($property);
    }

    // ------------------------------------------------------------------------

    public function __call($method, array $args = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
    }
}