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

namespace O2System\Framework\Cli\Commanders;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commander;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class Make
 *
 * @package O2System\Framework\Cli\Commanders
 */
class Make extends Commander
{
    use FilePathCollectorTrait;
    
    /**
     * Make::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Make::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_DESC';

    /**
     * Make::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'name'      => [
            'description' => 'CLI_MAKE_NAME_DESC',
            'required'    => true,
        ],
        'path'      => [
            'description' => 'CLI_MAKE_PATH_DESC',
            'required'    => false,
        ],
        'filename'  => [
            'description' => 'CLI_MAKE_FILENAME_DESC',
            'required'    => true,
        ],
        'namespace' => [
            'description' => 'CLI_MAKE_NAMESPACE_DESC',
            'shortcut'    => 'ns',
            'required'    => false,
        ],
    ];

    /**
     * Make::$path
     *
     * Make path.
     *
     * @var string
     */
    protected $optionPath = PATH_APP;

    /**
     * Make::$filename
     *
     * Make filename.
     *
     * @var string
     */
    protected $optionFilename;

    // ------------------------------------------------------------------------

    /**
     * Make::__reconstruct
     */
    public function __construct()
    {
        parent::__construct();

        $this->setFileDirName('PhpTemplateFiles');
        $this->addFilePaths([
            PATH_FRAMEWORK . 'Config' . DIRECTORY_SEPARATOR,
            PATH_APP . 'Config' . DIRECTORY_SEPARATOR,
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Make::optionPath
     *
     * @param string $path
     */
    public function optionPath($path)
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $path = PATH_APP . str_replace([PATH_APP, PATH_ROOT], '', $path);

        if (pathinfo($path, PATHINFO_EXTENSION)) {
            $this->optionFilename(pathinfo($path, PATHINFO_FILENAME));
            $path = dirname($path);
        }

        $this->optionPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Make::optionFilename
     *
     * @param string $name
     */
    public function optionFilename($name)
    {
        $pathinfo = pathinfo($name);
        $this->optionFilename = $pathinfo['filename'] . '.' . (empty($pathinfo['extension']) ? 'php' : $pathinfo['extension']);

        $this->optionPath = empty($pathinfo['dirname']) || $pathinfo['dirname'] === '.' ? $this->optionPath : $pathinfo['dirname'] . DIRECTORY_SEPARATOR;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Make::optionName
     *
     * @param string $name
     */
    public function optionName($name)
    {
        $this->optionFilename($name);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Make::optionNamespace
     *
     * @param string $namespace
     */
    public function optionNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }
}