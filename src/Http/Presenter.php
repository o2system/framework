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
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;
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
        if(isset($config['theme'])) {
            $this->setTheme($config['theme']);
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
        if($this->theme instanceof Theme) {
            $this->assets->removeFilePath($this->theme->getRealPath());
        }

        if (is_bool($theme)) {
            $this->theme = false;
        } elseif(($moduleTheme = modules()->top()->getTheme($theme, true)) instanceof Theme) {
            $this->theme = $moduleTheme;
        } elseif(($appTheme = modules()->first()->getTheme($theme, true)) instanceof Theme) {
            $this->theme = $appTheme;
        }

        if($this->theme) {
            if ( ! defined('PATH_THEME')) {
                define('PATH_THEME', $this->theme->getRealPath());
            }

            // add theme assets directory
            $this->assets->pushFilePath($this->theme->getRealPath());

            if(is_dir($themeViewPath = $this->theme->getRealPath() . 'views' . DIRECTORY_SEPARATOR)) {

                // add theme view directory
                view()->addFilePath($this->theme->getRealPath());

                // add theme output directory
                output()->pushFilePath($themeViewPath);

                $modules = modules()->getArrayCopy();

                foreach($modules as $module) {
                    if ( ! in_array($module->getType(), ['KERNEL', 'FRAMEWORK', 'APP'])) {
                        $moduleResourcesPath = str_replace(PATH_RESOURCES, '', $module->getResourcesDir());

                        if(is_dir($themeViewPath . $moduleResourcesPath)) {
                            view()->pushFilePath($themeViewPath . $moduleResourcesPath);
                        }
                    }
                }
            }
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
     * Presenter::include
     *
     * @param string $filename
     * @param array  $vars
     *
     * @return string
     */
    public function include($filename, array $vars = [])
    {
        return view()->load($filename, $vars, true);
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
