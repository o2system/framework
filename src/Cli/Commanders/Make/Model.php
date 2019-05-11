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
 * Class Model
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Model extends Make
{
    /**
     * Model::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_MODEL_DESC';

    // ------------------------------------------------------------------------

    /**
     * Model::execute
     */
    public function execute()
    {
        $this->__callOptions();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_MODEL_E_FILENAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        if (strpos($this->optionPath, 'Models') === false) {
            $filePath = $this->optionPath . 'Models' . DIRECTORY_SEPARATOR . $this->optionFilename;
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
                    ->setString(language()->getLine('CLI_MAKE_MODEL_E_EXISTS', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $className = prepare_class_name(pathinfo($filePath, PATHINFO_FILENAME));
        @list($namespaceDirectory, $subNamespace) = explode('Models', dirname($filePath));

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Models' . (empty($subNamespace)
                ? null
                : str_replace(
                    '/',
                    '\\',
                    $subNamespace
                )) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date('d/m/Y H:m');
        $vars[ 'NAMESPACE' ] = trim($classNamespace, '\\');
        $vars[ 'PACKAGE' ] = '\\' . trim($classNamespace, '\\');
        $vars[ 'CLASS' ] = $className;
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

namespace NAMESPACE;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class CLASS
 *
 * @package PACKAGE
 */
class CLASS extends Model
{
    public \$table = 'table_name';
}
PHPTEMPLATE;

        $fileContent = str_replace(array_keys($vars), array_values($vars), $phpTemplate);
        file_put_contents($filePath, $fileContent);

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_MODEL_S_MAKE', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }

}