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

namespace O2System\Framework\Cli\Commanders\Make;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commanders\Make;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Plugin
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Plugin extends Make
{
    /**
     * Plugin::$optionName
     *
     * @var string
     */
    public $optionName;

    /**
     * Plugin::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_MODULE_DESC';

    // ------------------------------------------------------------------------

    /**
     * Plugin::optionName
     *
     * @param string $name
     */
    public function optionName($name)
    {
        if (empty($this->optionPath)) {
            $this->optionPath = PATH_APP . 'Modules' . DIRECTORY_SEPARATOR;
        }

        $this->optionName = $name;
    }

    // ------------------------------------------------------------------------

    /**
     * Plugin::execute
     */
    public function execute()
    {
        $this->__callOptions();

        if (empty($this->optionName)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_MODULE_E_NAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $moduleType = empty($this->moduleType)
            ? 'Plugins'
            : ucfirst(plural($this->moduleType));

        if (strpos($this->optionPath, $moduleType) === false) {
            $modulePath = $this->optionPath . $moduleType . DIRECTORY_SEPARATOR . $this->optionName . DIRECTORY_SEPARATOR;
        } else {
            $modulePath = $this->optionPath . $this->optionName . DIRECTORY_SEPARATOR;
        }

        if ( ! is_dir($modulePath)) {
            mkdir($modulePath, 0777, true);
        } else {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_PLUGIN_E_EXISTS', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $jsonProperties[ 'name' ] = readable(
            pathinfo($modulePath, PATHINFO_FILENAME),
            true
        );

        if (empty($this->namespace)) {
            @list($moduleDirectory, $moduleName) = explode($moduleType, dirname($modulePath));
            $namespace = loader()->getDirNamespace($moduleDirectory) .
                $moduleType . '\\' . prepare_class_name(
                    $this->optionName
                ) . '\\';
        } else {
            $namespace = $this->namespace;
            $jsonProperties[ 'namespace' ] = rtrim($namespace, '\\') . '\\';
        }

        $jsonProperties[ 'created' ] = date('d M Y');

        loader()->addNamespace($namespace, $modulePath);

        $fileContent = json_encode($jsonProperties, JSON_PRETTY_PRINT);

        $filePath = $modulePath . strtolower(singular($moduleType)) . '.json';

        file_put_contents($filePath, $fileContent);

        $this->optionPath = $modulePath;
        $this->optionFilename = prepare_filename($this->optionName) . '.php';

        (new Controller())
            ->optionPath($this->optionPath)
            ->optionFilename($this->optionFilename);

        if (is_dir($modulePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_PLUGIN_S_MAKE', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}