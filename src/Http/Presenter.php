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

use O2System\Framework\Containers\Modules\DataStructures\Module\Theme;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;
use O2System\Spl\DataStructures\SplArrayObject;
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
     * Presenter::$meta
     *
     * @var Presenter\Meta
     */
    public $meta;

    /**
     * Presenter::$page
     *
     * @var Presenter\Page
     */
    public $page;

    /**
     * Presenter::$assets
     *
     * @var Presenter\Assets
     */
    public $assets;

    /**
     * Presenter::$partials
     *
     * @var Presenter\Repositories\Partials
     */
    public $partials;

    /**
     * Presenter::$widgets
     *
     * @var Presenter\Repositories\Widgets
     */
    public $widgets;

    /**
     * Presenter::$theme
     *
     * @var bool|Theme
     */
    public $theme = false;

    // ------------------------------------------------------------------------

    /**
     * Presenter::__construct
     */
    public function __construct()
    {
        loader()->helper('Url');

        $this->meta = new Presenter\Meta;
        $this->page = new Presenter\Page;
        $this->assets = new Presenter\Assets;
        $this->partials = new Presenter\Repositories\Partials();
        $this->widgets = new Presenter\Repositories\Widgets();
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::initialize
     *
     * @param array $config
     *
     * @return static
     */
    public function initialize(array $config = [])
    {
        if (count($config)) {
            $this->setConfig($config);
        } elseif (false !== ($config = config('view')->presenter)) {
            $this->setConfig($config);
        }

        // autoload presenter assets
        if (isset($config[ 'assets' ])) {
            $this->assets->autoload($config[ 'assets' ]);
        }

        // autoload presenter theme
        if (isset($config[ 'theme' ])) {
            if (false !== ($theme = $config[ 'theme' ])) {
                $this->setTheme($theme);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::setTheme
     *
     * @param string $theme
     *
     * @return static
     */
    public function setTheme($theme)
    {
        if (is_bool($theme)) {
            $this->theme = false;
        } elseif (($this->theme = modules()->current()->getTheme($theme)) instanceof Theme) {
            $pathTheme = str_replace(PATH_RESOURCES, PATH_PUBLIC, $this->theme->getRealPath());

            if ( ! defined('PATH_THEME')) {
                define('PATH_THEME', $pathTheme);
            }

            // add theme view directory
            view()->addFilePath($this->theme->getRealPath());

            // add theme output directory
            output()->setFileDirName('views'); // replace views folder base on theme structure
            output()->addFilePath($this->theme->getRealPath(), 'theme');
            output()->setFileDirName('Views'); // restore Views folder base on PSR-4 folder structure

            // add public theme directory
            loader()->addPublicDir($pathTheme, 'theme');

            // add public theme assets directory
            loader()->addPublicDir($pathTheme . 'assets' . DIRECTORY_SEPARATOR, 'themeAssets');

            // load theme and layout
            $this->theme->load();
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::store
     *
     * @param string $offset
     * @param mixed  $value
     * @param bool   $replace
     */
    public function store($offset, $value, $replace = false)
    {
        if ($value instanceof \Closure) {
            parent::store($offset, call_user_func($value, $this));
        } else {
            parent::store($offset, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::getArrayCopy
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $storage = $this->storage;

        // Add Properties
        $storage[ 'meta' ] = $this->meta;
        $storage[ 'page' ] = $this->page;
        $storage[ 'assets' ] = new SplArrayObject([
            'head' => $this->assets->getHead(),
            'body' => $this->assets->getBody(),
        ]);
        $storage[ 'partials' ] = $this->partials;
        $storage[ 'widgets' ] = $this->widgets;
        $storage[ 'theme' ] = $this->theme;

        // Add Services
        $storage[ 'config' ] = config();
        $storage[ 'language' ] = language();
        $storage[ 'session' ] = session();
        $storage[ 'presenter' ] = presenter();
        $storage[ 'input' ] = input();

        if (services()->has('csrfProtection')) {
            $storage[ 'csrfToken' ] = services()->get('csrfProtection')->getToken();
        }

        return $storage;
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::get
     *
     * @param string $property
     *
     * @return mixed
     */
    public function get($property)
    {
        // CodeIgniter property aliasing
        if ($property === 'load') {
            $property = 'loader';
        }

        if (services()->has($property)) {
            return services()->get($property);
        } elseif ($property === 'model') {
            return models('controller');
        } elseif ($property === 'services' || $property === 'libraries') {
            return services();
        } elseif (method_exists($this, $property)) {
            return call_user_func([&$this, $property]);
        }

        return parent::get($property);
    }

    // ------------------------------------------------------------------------

    /**
     * Presenter::__call
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
    }
}
