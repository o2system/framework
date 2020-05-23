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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\DataStructures;
use O2System\Kernel\Cli\Writers\Format;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Language
 *
 * @package O2System\Framework\Services
 */
class Language extends \O2System\Kernel\Services\Language
{
    /**
     * Language::$options
     *
     * @var array
     */
    protected $options = [];

    // ------------------------------------------------------------------------

    /**
     * Language::__construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->addFilePaths([PATH_FRAMEWORK, PATH_APP]);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::setOptions
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::addOption
     *
     * @param string $option
     * @param string $label
     */
    public function addOption($option, $label)
    {
        if (!$this->hasOption($option)) {
            $this->options[$option] = $label;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::hasOption
     *
     * @param string $option
     * @return bool
     */
    public function hasOption($option)
    {
        return array_key_exists($option, $this->options);
    }
}