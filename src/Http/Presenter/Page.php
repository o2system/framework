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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Breadcrumb;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Kernel\Http\Message\Uri;
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Page
 *
 * @package O2System\Framework\Http\Presenter
 */
class Page extends AbstractRepository
{
    /**
     * Page::$file
     *
     * @var \O2System\Spl\Info\SplFileInfo
     */
    public $file;

    /**
     * Page::$uri
     *
     * @var Uri
     */
    public $uri;

    /**
     * Page::$breadcrumb
     *
     * @var Breadcrumb
     */
    public $breadcrumb;

    /**
     * Page Variables
     *
     * @var array
     */
    private $vars = [];

    /**
     * Page Presets
     *
     * @var SplArrayObject
     */
    private $presets;

    // ------------------------------------------------------------------------

    /**
     * Page::__construct
     */
    public function __construct()
    {
        // Create Page breadcrumbs
        $this->breadcrumb = new Breadcrumb();

        // Store Page Uri
        $this->uri = new Uri();
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setVars
     *
     * @param array $vars
     *
     * @return static
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::getVars
     *
     * Gets page variables.
     *
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setPresets
     *
     * @param \O2System\Spl\DataStructures\SplArrayObject $presets
     *
     * @return static
     */
    public function setPresets(SplArrayObject $presets)
    {
        $this->presets = $presets;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::getPresets
     *
     * Gets page presets.
     *
     * @return mixed Returns FALSE if failed.
     */
    public function getPresets($offset = null)
    {
        if ($this->presets instanceof SplArrayObject) {
            if(empty($offset)) {
                return $this->presets;
            }

            if($this->presets->offsetExists($offset)) {
                return $this->presets->offsetGet($offset);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::addPreset
     *
     * Gets page presets.
     *
     * @return static
     */
    public function addPreset($key, $value)
    {
        if ( ! $this->presets instanceof SplArrayObject) {
            $this->presets = new SplArrayObject();
        }

        $this->presets->offsetSet($key, $value);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setFile
     *
     * @param $filePath
     *
     * @return static
     */
    public function setFile($filePath)
    {
        if (is_file($filePath)) {
            $this->file = new SplFileInfo($filePath);

            if (file_exists(
                $propertiesFilePath = $this->file->getPath() . DIRECTORY_SEPARATOR . str_replace(
                        '.phtml',
                        '.json',
                        strtolower($this->file->getBasename())
                    )
            )) {
                $properties = file_get_contents($propertiesFilePath);
                $properties = json_decode($properties);

                if (isset($properties->vars)) {
                    $this->vars = get_object_vars($properties->vars);
                }

                if (isset($properties->presets)) {
                    $this->presets = new SplArrayObject(get_object_vars($properties->presets));

                    if(isset($this->presets->title)) {
                        $this->setHeader($this->presets->title);
                    }

                    if(isset($this->presets->description)) {
                        $this->setDescription($this->presets->description);
                    }

                    if(isset($this->presets->content)) {
                        $this->setContent($this->presets->content);
                    }
                }
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setHeader
     *
     * @param string $header
     *
     * @return static
     */
    public function setHeader($header)
    {
        $header = trim($header);
        $header = language($header);
        $this->store('header', $header);
        presenter()->meta->title->append($header);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setTitle
     *
     * @param string $title
     *
     * @return static
     */
    public function setTitle($title)
    {
        $title = trim($title);
        $title = language($title);
        $this->store('title', $title);
        presenter()->meta->title->append($title);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setDescription
     *
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description)
    {
        $description = trim($description);
        $this->store('description', $description);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setContent
     *
     * @param string $content
     *
     * @return static
     */
    public function setContent($content)
    {
        if ( ! empty($content)) {
            presenter()->partials->offsetSet('content', $content);
        }

        return $this;
    }
}