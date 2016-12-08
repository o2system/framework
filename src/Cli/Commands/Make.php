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

namespace O2System\Framework\Cli\Commands;

// ------------------------------------------------------------------------

use O2System\Framework\Abstracts\AbstractCommand;
use O2System\Kernel\Spl\Exceptions\Logic\OutOfRangeException;

/**
 * Class Make
 *
 * @package O2System\Framework\Cli\Commands
 */
class Make extends AbstractCommand
{
    protected $version = '1.0.0';

    /**
     * Registry::$name
     *
     * Command name.
     *
     * @var string
     */
    protected $caller = 'make';

    /**
     * Registry::$description
     *
     * Command description.
     *
     * @var string
     */
    protected $description = 'PHP template file generator';

    /**
     * Registry::$options
     *
     * Command options.
     *
     * @var array
     */
    protected $options = [
        'controller' => [
            'description' => 'Generate controller class template',
        ],
        'presenter'  => [
            'description' => 'Generate presenter class template',
        ],
        'model'      => [
            'description' => 'Generate model class template',
        ],
        'library'    => [
            'description' => 'Generate library class template',
        ],
        'helper'     => [
            'description' => 'Generate helper file template',
        ],
        'config'     => [
            'description' => 'Generate config file template',
        ],
        'module'     => [
            'description' => 'Generate modular package template',
        ],
        'widget'     => [
            'description' => 'Generate widget package template',
        ],
    ];

    /**
     * Make::$path
     *
     * Make path.
     *
     * @var string
     */
    protected $path;

    /**
     * Make::$filename
     *
     * Make filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Make::$fileType
     *
     * Make file type.
     *
     * @var string
     */
    protected $fileType;

    public function optionPath ( $path )
    {
        $path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $path );
        $path = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

        $this->path = $path;

        return true;
    }

    public function optionController ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'CONTROLLER';

        return true;
    }

    public function optionPresenter ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'PRESENTER';

        return true;
    }

    public function optionModel ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'MODEL';

        return true;
    }

    public function optionLibrary ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'LIBRARY';

        return true;
    }

    public function optionHelper ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'HELPER';

        return true;
    }

    public function optionConfig ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = prepare_filename( $name ) . '.php';
        $this->fileType = 'CONFIG';

        return true;
    }

    public function optionOrm ( $useOrm = true )
    {
        $this->isUseORM = $useOrm;
    }

    public function optionModule ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = studlycapcase( $name );
        $this->fileType = 'MODULE';
    }

    public function optionWidget ( $name )
    {
        if ( empty( $this->path ) ) {
            $this->optionPath( PATH_APP );
        }

        $this->filename = studlycapcase( $name );
        $this->fileType = 'WIDGET';
    }

    public function optionType ( $type )
    {
        $this->moduleType = strtolower( $type );
    }

    public function optionNamespace ( $namespace )
    {
        $this->namespace = $namespace;
    }

    protected function execute ()
    {
        $writeMethod = 'write' . studlycapcase( strtolower( $this->fileType ) );

        if ( method_exists( $this, $writeMethod ) ) {
            call_user_func( [ &$this, $writeMethod ] );
        }
    }

    private function writeController ()
    {
        if ( strpos( $this->path, 'Controllers' ) === false ) {
            $filePath = $this->path . 'Controllers' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
        @list( $namespaceDirectory, $subNamespace ) = explode( 'Controllers', dirname( $filePath ) );

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Controllers' . ( empty( $subNamespace )
                ? null
                : str_replace(
                    '/',
                    '\\',
                    $subNamespace
                ) ) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'NAMESPACE' ] = trim( $classNamespace, '\\' );
        $vars[ 'PACKAGE' ] = '\\' . trim( $classNamespace, '\\' );
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

use O2System\Framework\Http\Controller;

/**
 * Class CLASS
 *
 * @package PACKAGE
 */
class CLASS extends Controller
{
    public function index()
    {
        // TODO: Change the autogenerated stub
    }
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writePresenter ()
    {
        if ( strpos( $this->path, 'Presenters' ) === false ) {
            $filePath = $this->path . 'Presenters' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
        @list( $namespaceDirectory, $subNamespace ) = explode( 'Presenters', dirname( $filePath ) );

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Presenters' . ( empty( $subNamespace )
                ? null
                : str_replace(
                    '/',
                    '\\',
                    $subNamespace
                ) ) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'NAMESPACE' ] = trim( $classNamespace, '\\' );
        $vars[ 'PACKAGE' ] = '\\' . trim( $classNamespace, '\\' );
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

use O2System\Framework\Http\Presenter;

/**
 * Class CLASS
 *
 * @package PACKAGE
 */
class CLASS extends Presenter
{
    
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writeModel ()
    {
        if ( strpos( $this->path, 'Models' ) === false ) {
            $filePath = $this->path . 'Models' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
        @list( $namespaceDirectory, $subNamespace ) = explode( 'Models', dirname( $filePath ) );

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Models' . ( empty( $subNamespace )
                ? null
                : str_replace(
                    '/',
                    '\\',
                    $subNamespace
                ) ) . '\\';

        $isUseORM = empty( $this->isUseORM ) ? false : true;

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'NAMESPACE' ] = trim( $classNamespace, '\\' );
        $vars[ 'PACKAGE' ] = '\\' . trim( $classNamespace, '\\' );
        $vars[ 'CLASS' ] = $className;
        $vars[ 'FILEPATH' ] = $filePath;
        $vars[ 'EXTEND' ] = $isUseORM
            ? 'Orm'
            : 'Framework';

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

namespace NAMESPACE;

// ------------------------------------------------------------------------

use O2System\EXTEND\Abstracts\AbstractModel;

/**
 * Class CLASS
 *
 * @package PACKAGE
 */
class CLASS extends AbstractModel
{
    public function index()
    {
        // TODO: Change the autogenerated stub
    }
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writeLibrary ()
    {
        if ( strpos( $this->path, 'Libraries' ) === false ) {
            $filePath = $this->path . 'Libraries' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $className = prepare_class_name( pathinfo( $filePath, PATHINFO_FILENAME ) );
        @list( $namespaceDirectory, $subNamespace ) = explode( 'Libraries', dirname( $filePath ) );

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Libraries' . ( empty( $subNamespace )
                ? null
                : str_replace(
                    '/',
                    '\\',
                    $subNamespace
                ) ) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'NAMESPACE' ] = trim( $classNamespace, '\\' );
        $vars[ 'PACKAGE' ] = '\\' . trim( $classNamespace, '\\' );
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

/**
 * Class CLASS
 *
 * @package PACKAGE
 */
class CLASS
{
    public function __construct()
    {
        // TODO: Change the autogenerated stub
    }
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writeHelper ()
    {
        if ( strpos( $this->path, 'Helpers' ) === false ) {
            $filePath = $this->path . 'Helpers' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'HELPER' ] = underscore(
            decamelcase(
                pathinfo( $filePath, PATHINFO_FILENAME )
            )
        );
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

if ( ! function_exists( 'HELPER' ) ) {
    /**
     * HELPER
     */
    function HELPER() {
    }
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writeConfig ()
    {
        if ( strpos( $this->path, 'Config' ) === false ) {
            $filePath = $this->path . 'Config' . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $filePath = $this->path . $this->filename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            throw new OutOfRangeException( 'File already exists' );
        }

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'CONFIG' ] = '$' . camelcase( pathinfo( $filePath, PATHINFO_FILENAME ) );
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

CONFIG = [];
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );
    }

    private function writeModule ()
    {
        $moduleType = empty( $this->moduleType )
            ? 'Modules'
            : ucfirst( plural( $this->moduleType ) );

        if ( strpos( $this->path, $moduleType ) === false ) {
            $modulePath = $this->path . $moduleType . DIRECTORY_SEPARATOR . $this->filename . DIRECTORY_SEPARATOR;
        } else {
            $modulePath = $this->path . $this->filename . DIRECTORY_SEPARATOR;
        }

        if ( ! is_dir( $modulePath ) ) {
            mkdir( $modulePath, 777, true );
        }

        $jsProps[ 'name' ] = readable(
            pathinfo( $modulePath, PATHINFO_FILENAME ),
            true
        );

        if ( empty( $this->namespace ) ) {
            @list( $moduleDirectory, $moduleName ) = explode( $moduleType, dirname( $modulePath ) );
            $namespace = loader()->getDirNamespace( $moduleDirectory ) .
                         $moduleType . '\\' . prepare_class_name(
                             $this->filename
                         ) . '\\';
        } else {
            $namespace = prepare_class_name( $this->namespace );
            $jsProps[ 'namespace' ] = rtrim( $namespace, '\\' ) . '\\';
        }

        $jsProps[ 'created' ] = date( 'd M Y' );

        loader()->addNamespace( $namespace, $modulePath );

        $fileContent = json_encode( $jsProps, JSON_PRETTY_PRINT );

        $filePath = $modulePath . strtolower( singular( $moduleType ) ) . '.jsprop';

        file_put_contents( $filePath, $fileContent );

        $this->path = $modulePath;
        $this->filename = prepare_filename( $this->filename ) . '.php';

        $this->writeController();
    }

    private function writeWidget ()
    {
        if ( strpos( $this->path, 'Widgets' ) === false ) {
            $widgetPath = $this->path . 'Widgets' . DIRECTORY_SEPARATOR . $this->filename . DIRECTORY_SEPARATOR;
        } else {
            $widgetPath = $this->path . $this->filename . DIRECTORY_SEPARATOR;
        }

        if ( ! is_dir( $widgetPath ) ) {
            mkdir( $widgetPath, 777, true );
        }

        $jsProps[ 'name' ] = readable(
            pathinfo( $widgetPath, PATHINFO_FILENAME ),
            true
        );

        if ( empty( $this->namespace ) ) {
            @list( $moduleDirectory, $moduleName ) = explode( 'Widgets', dirname( $widgetPath ) );
            $namespace = loader()->getDirNamespace( $moduleDirectory ) .
                         'Widgets' . '\\' . prepare_class_name(
                             $this->filename
                         ) . '\\';
        } else {
            $namespace = prepare_class_name( $this->namespace );
            $jsProps[ 'namespace' ] = rtrim( $namespace, '\\' ) . '\\';
        }

        $jsProps[ 'created' ] = date( 'd M Y' );

        loader()->addNamespace( $namespace, $widgetPath );

        $fileContent = json_encode( $jsProps, JSON_PRETTY_PRINT );

        $filePath = $widgetPath . 'widget.jsprop';

        file_put_contents( $filePath, $fileContent );

        $this->path = $widgetPath;
        $this->filename = prepare_filename( $this->filename ) . '.php';

        $this->writePresenter();
    }
}