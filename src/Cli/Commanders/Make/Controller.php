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
 * Class Controller
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Controller extends Make
{
    /**
     * Controller::$reconstruct
     *
     * @var bool
     */
    protected $http = false;

    /**
     * Controller::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_CONTROLLER_DESC';

    // ------------------------------------------------------------------------

    /**
     * Controller::addReconstruct
     *
     * @return static
     */
    public function isHttp()
    {
        $this->http = true;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::execute
     *
     * @throws \ReflectionException
     */
    public function execute()
    {
        $this->__callOptions();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_CONTROLLER_E_FILENAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $controllerDir = 'Controllers';
        $controllerTemplateFile = 'Controller.tpl';
        $messageSuccess = 'CLI_MAKE_CONTROLLER_S_MAKE';
        $messageError = 'CLI_MAKE_CONTROLLER_E_MAKE';

        if($this->http) {
            $controllerDir = 'Http';
            $controllerTemplateFile = 'HttpController.tpl';
            $messageSuccess = 'CLI_MAKE_HTTP_CONTROLLER_S_MAKE';
            $messageError = 'CLI_MAKE_HTTP_CONTROLLER_S_MAKE';
            $this->optionFilename = 'Controller.php';
        }

        if (strpos($this->optionPath, $controllerDir) === false) {
            $filePath = $this->optionPath . $controllerDir . DIRECTORY_SEPARATOR . $this->optionFilename;
        } else {
            $filePath = $this->optionPath . $this->optionFilename;
        }

        $fileDirectory = dirname($filePath) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine($messageError, [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $className = prepare_class_name(pathinfo($filePath, PATHINFO_FILENAME));
        @list($namespaceDirectory, $subNamespace) = explode($controllerDir, str_replace(['\\','/'], '\\', dirname($filePath)));
        $subNamespace = rtrim($subNamespace, '\\');

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . $controllerDir . (empty($subNamespace)
                ? null
                : $subNamespace) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date('d/m/Y H:m');
        $vars[ 'NAMESPACE' ] = trim($classNamespace, '\\');
        $vars[ 'PACKAGE' ] = '\\' . trim($classNamespace, '\\');
        $vars[ 'CLASS' ] = $className;
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplateFilePaths = $this->getFilePaths(true);

        foreach($phpTemplateFilePaths as $phpTemplateFilePath)
        {
            if(is_file($phpTemplateFilePath .$controllerTemplateFile)) {
                $phpTemplate = file_get_contents($phpTemplateFilePath . $controllerTemplateFile);
                break;
            }
        }

        $fileContent = str_replace(array_keys($vars), array_values($vars), $phpTemplate);
        file_put_contents($filePath, $fileContent);

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine($messageSuccess, [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}