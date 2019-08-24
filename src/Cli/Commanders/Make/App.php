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
 * Class App
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class App extends Make
{
    /**
     * App::$optionName
     *
     * @var string
     */
    public $optionName;

    /**
     * App::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_APP_DESC';

    // ------------------------------------------------------------------------

    /**
     * App::optionName
     *
     * @param string $name
     */
    public function optionName($name)
    {
        $this->optionName = $name;
    }

    // ------------------------------------------------------------------------

    /**
     * App::execute
     */
    public function execute()
    {
        $this->__callOptions();

        if (empty($this->optionName)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_APP_E_NAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $moduleType = empty($this->moduleType)
            ? 'App'
            : ucfirst(plural($this->moduleType));

        $modulePath = PATH_APP . studlycase($this->optionName) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($modulePath)) {
            mkdir($modulePath, 0777, true);
        } else {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_APP_E_EXISTS', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $jsonProperties[ 'name' ] = readable(
            pathinfo($modulePath, PATHINFO_FILENAME),
            true
        );

        if (empty($this->namespace)) {
            $namespace = loader()->getDirNamespace('') .
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
                    ->setString(language()->getLine('CLI_MAKE_APP_S_MAKE', [$modulePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}