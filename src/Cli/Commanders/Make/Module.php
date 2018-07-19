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

namespace O2System\Framework\Cli\Commanders\Make;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commanders\Make;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Module
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Module extends Make
{
    public $optionName;

    /**
     * Module::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_MODULE_DESC';

    public function optionName($name)
    {
        if(empty($this->optionPath)) {
            $this->optionPath = PATH_APP . 'Modules' . DIRECTORY_SEPARATOR;
        }

        $this->optionName = $name;

        return $this;
    }

    public function execute()
    {
        parent::execute();

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
            ? 'Modules'
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
                    ->setString(language()->getLine('CLI_MAKE_MODULE_E_EXISTS', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $jsProps[ 'name' ] = readable(
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
            $jsProps[ 'namespace' ] = rtrim($namespace, '\\') . '\\';
        }

        $jsProps[ 'created' ] = date('d M Y');

        loader()->addNamespace($namespace, $modulePath);

        $fileContent = json_encode($jsProps, JSON_PRETTY_PRINT);

        $filePath = $modulePath . strtolower(singular($moduleType)) . '.jsprop';

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
                    ->setString(language()->getLine('CLI_MAKE_MODULE_S_MAKE', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}