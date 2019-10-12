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
use O2System\Framework\Models\Sql\Model;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Migrate
 * @package O2System\Framework\Cli\Commanders
 */
class Migrate extends Commander
{
    /**
     * Migrate::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Migrate::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MIGRATION_DESC';

    /**
     * Migrate::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'version'  => [
            'description' => 'CLI_MIGRATION_VERSION_HELP',
            'required'    => false,
        ],
        'reset'    => [
            'description' => 'CLI_MIGRATION_RESET_HELP',
            'required'    => true,
        ],
        'rollback' => [
            'description' => 'CLI_MIGRATION_ROLLBACK_HELP',
            'required'    => false,
        ],
        'refresh'  => [
            'description' => 'CLI_MIGRATION_REFRESH_HELP',
            'required'    => false,
        ],
        'fresh'    => [
            'description' => 'CLI_MIGRATION_FRESH_HELP',
            'required'    => false,
        ],
        'seed'     => [
            'description' => 'CLI_MIGRATION_SEED_HELP',
            'required'    => false,
        ],
    ];

    /**
     * Migrate::$model
     *
     * @var \O2System\Framework\Models\Sql\Model|\O2System\Framework\Models\NoSql\Model
     */
    protected $model;

    /**
     * Migrate::$optionGroup
     *
     * @var string
     */
    protected $optionGroup = 'default';

    /**
     * Migrate::$optionSeed
     *
     * @var bool
     */
    protected $optionSeed = true;

    /**
     * Migrate::$optionSql
     *
     * @var string
     */
    protected $optionSql;

    /**
     * Migrate::$optionFilename
     *
     * @var string
     */
    protected $optionFilename;

    /**
     * Migrate::$optionBatch
     *
     * @var int
     */
    protected $optionBatch;

    // ------------------------------------------------------------------------

    /**
     * Migrate::optionGroup
     *
     * @param string $group
     */
    public function optionGroup($group)
    {
        if (database()->exists($group)) {
            $this->optionGroup = $group;
        } else {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_DATABASE_GROUP_NOT_EXISTS', [$group]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::optionReset
     */
    public function optionReset()
    {
        $this->optionReset = true;
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::optionRefresh
     *
     * @param string $sql
     */
    public function optionSql($sql)
    {
        $this->optionSql = $sql;
    }

    /**
     * Migrate::optionFilename
     *
     * @param string $filename
     */
    public function optionFilename($filename)
    {
        $this->optionFilename = $filename;
    }

    /**
     * Migrate::optionBatch
     *
     * @param int $batch
     */
    public function optionBatch($batch)
    {
        $this->optionBatch = intval($batch);
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::optionRefresh
     */
    public function optionSeed()
    {
        $this->optionSeed = true;
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::__call
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        $this->setup(); // always run setup first

        return parent::__call($method, $args);
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::setup
     */
    protected function setup()
    {
        /**
         * Setup System Migrate Database
         */
        $db = database()->loadConnection($this->optionGroup);

        if (method_exists($db, 'hasTable')) {
            // SQL Migrate Setup
            if ( ! $db->hasTable('sys_migrations')) {
                $forge = $db->getForge();
                $forge->createTable('sys_migrations', [
                    'id'                      => [
                        'type'           => 'INT',
                        'length'         => 10,
                        'unsigned'       => true,
                        'auto_increment' => true,
                        'null'           => false,
                        'primary_key'    => true,
                    ],
                    'filename'                => [
                        'type'   => 'VARCHAR',
                        'length' => 255,
                        'null'   => false,
                    ],
                    'version'                 => [
                        'type'    => 'VARCHAR',
                        'length'  => 255,
                        'null'    => false,
                        'default' => 'v0.0.0',
                    ],
                    'timestamp'               => [
                        'type' => 'datetime',
                        'null' => false,
                    ],
                    'batch'                   => [
                        'type'     => 'INT',
                        'length'   => 10,
                        'unsigned' => true,
                        'null'     => false,
                        'default'  => 0,
                    ],
                    'record_status'           => [
                        'type'    => 'enum',
                        'value'   => ['DELETE', 'TRASH', 'DRAFT', 'UNPUBLISH', 'PUBLISH'],
                        'null'    => false,
                        'default' => 'PUBLISH',
                    ],
                    'record_create_timestamp' => [
                        'type' => 'datetime',
                        'null' => true,
                    ],
                    'record_create_user'      => [
                        'type'     => 'int',
                        'length'   => 10,
                        'unsigned' => true,
                        'null'     => true,
                        'default'  => 0,
                    ],
                    'record_update_timestamp' => [
                        'type'      => 'datetime',
                        'null'      => true,
                        'timestamp' => true,
                    ],
                    'record_update_user'      => [
                        'type'     => 'int',
                        'length'   => 10,
                        'unsigned' => true,
                        'null'     => true,
                        'default'  => 0,
                    ],
                ], true);
            }

            $this->model = new class extends Model
            {
                /**
                 * Model::$table
                 *
                 * @var string
                 */
                public $table = 'sys_migrations';

                /**
                 * Model::$visibleColumns
                 *
                 * @var array
                 */
                public $visibleColumns = [
                    'id',
                    'filename',
                    'version',
                    'timestamp',
                    'batch',
                ];

                // ------------------------------------------------------------------------

                /**
                 * Model::__construct
                 */
                public function register()
                {
                    $batch = $this->getLatestBatch();
                    $files = glob(PATH_DATABASE . "migrations/*.php");

                    foreach ($files as $filename) {
                        $sets = [
                            'filename'  => $this->getFilename($filename),
                            'version'   => $this->getFileVersion($filename),
                            'timestamp' => $this->getFileTimestamp($filename),
                            'batch'     => $batch,
                        ];

                        $this->insertOrUpdate($sets, ['filename' => $sets[ 'filename' ]]);
                    }
                }

                // ------------------------------------------------------------------------

                /**
                 * Migrate::getFilename
                 *
                 * Extracts the migration timestamp from a filename
                 *
                 * @param string $filename
                 *
                 * @return false|string
                 */
                protected function getFilename($filename)
                {
                    return str_replace(PATH_DATABASE . 'migrations' . DIRECTORY_SEPARATOR, '', $filename);
                }

                // ------------------------------------------------------------------------

                /**
                 * Migrate::getFileTimestamp
                 *
                 * Extracts the migration timestamp from a filename
                 *
                 * @param string $filename
                 *
                 * @return false|string
                 */
                protected function getFileTimestamp($filename)
                {
                    $timestamp = filemtime($filename);
                    preg_match('/\d{4}[-]?\d{2}[-]?\d{2}[-]?\d{2}[-]?\d{2}[-]?\d{2}/', $filename, $matches);

                    $timestamp = count($matches) ? strtotime($matches[ 0 ]) : $timestamp;

                    return date('Y-m-d H:i:s', $timestamp);
                }

                // ------------------------------------------------------------------------

                /**
                 * Migrate::getFileVersion
                 *
                 * Extracts the migration timestamp from a filename
                 *
                 * @param string $filename
                 *
                 * @return false|string
                 */
                protected function getFileVersion($filename)
                {
                    $version = 'v0.0.0';
                    preg_match('/v\d*[.]?\d*[.]\d*/', $filename, $matches);

                    return count($matches) ? $matches[ 0 ] : $version;
                }

                // ------------------------------------------------------------------------

                /**
                 * Model::getLatestBatch
                 */
                public function getLatestBatch()
                {
                    $batch = 1;
                    if ($result = $this->qb->table('sys_migrations')->max('batch', 'lastBatch')->get()) {
                        if ($result->count()) {
                            $batch = (int)$result->first()->lastBatch;
                        }
                    }

                    return $batch == 0 ? 1 : $batch;
                }
            };

            $this->model->register();
        } elseif (method_exists($db, 'hasCollection')) {
            // NoSQL Migrate Setup
            if ( ! $db->hasCollection('sys_migrations')) {
                // Coming Soon
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::import
     */
    public function import()
    {
        if( ! empty($this->optionSql)) {
            $filePath = PATH_DATABASE . str_replace(['/','\\'], DIRECTORY_SEPARATOR, $this->optionSql);

            if(is_file($filePath)) {
                $sqlStatement = file_get_contents($filePath);

                if($this->model->db->query($sqlStatement)) {

                } else {

                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::latest
     */
    public function latest()
    {
        if($result = $this->model->findWhere(['batch' => $this->model->getLatestBatch()])) {
            if($result->count()) {
                foreach($result as $row) {
                    $this->run($row->filename, 'up');
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::rollback
     */
    public function rollback()
    {
        $latestBatch = $this->model->getLatestBatch();
        $requestBatch = ($latestBatch - 1);
        $requestBatch = $requestBatch < 1 ? 1 : $requestBatch;

        if( ! empty($this->optionBatch) ) {
            $requestBatch = $this->optionBatch;
        }

        $batches = range($requestBatch, $latestBatch, 1);
        $batches = array_reverse($batches);

        foreach($batches as $batch) {
            if($result = $this->model->findWhere(['batch' => $batch])) {
                if($result->count()) {
                    foreach($result as $row) {
                        $this->run($row->filename, 'down');
                        $this->run($row->filename, 'up');
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::refresh
     */
    public function refresh()
    {
        $batch = $this->model->getLatestBatch();
        $batches = range(1, $batch, 1);
        $batches = array_reverse($batches);

        foreach($batches as $batch) {
            if($result = $this->model->findWhere(['batch' => $batch])) {
                if($result->count()) {
                    foreach($result as $row) {
                        $this->run($row->filename, 'down');
                        $this->run($row->filename, 'up');
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::refresh
     */
    public function fresh()
    {
        if($result = $this->model->findWhere(['batch' => 1])) {
            if($result->count()) {
                foreach($result as $row) {
                    $this->run($row->filename, 'up');
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::run
     *
     * @param string $filename
     * @param string $method
     *
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function run($filename, $method = 'up')
    {
        $filePaths = [
            PATH_DATABASE . 'migrations' . DIRECTORY_SEPARATOR . str_replace(['/','\\'], DIRECTORY_SEPARATOR, $filename),
            PATH_DATABASE . str_replace(['/','\\'], DIRECTORY_SEPARATOR, $filename)
        ];

        foreach($filePaths as $filePath) {
            if(is_file($filePath)) {
                if(pathinfo($filePath, PATHINFO_EXTENSION) == 'php') {
                    require_once($filePath);
                    $filename = pathinfo($filePath, PATHINFO_FILENAME);
                    $filename = explode('_', $filename);
                    array_shift($filename);
                    $filename = implode('_', $filename);

                    $className = studlycase($filename);

                    output()->write(
                        (new Format())
                            ->setContextualClass(Format::INFO)
                            ->setString(language()->getLine('CLI_MIGRATION_RUN_FILENAME', [$filename]))
                            ->setNewLinesAfter(1)
                    );

                    if(class_exists($className)) {
                        $migration = new $className();

                        if(method_exists($migration, $method)) {
                            call_user_func([$migration, $method]);

                            output()->write(
                                (new Format())
                                    ->setContextualClass(Format::SUCCESS)
                                    ->setString(language()->getLine('CLI_MIGRATION_RUN_'.strtoupper($method).'_SUCCESS', [$filename]))
                                    ->setNewLinesAfter(1)
                            );

                            if($method === 'up') {
                                if(method_exists($migration, 'seed')) {
                                    if($this->optionSeed) {
                                        call_user_func([$migration, 'seed']);

                                        output()->write(
                                            (new Format())
                                                ->setContextualClass(Format::SUCCESS)
                                                ->setString(language()->getLine('CLI_MIGRATION_RUN_SEED_SUCCESS', [$filename]))
                                                ->setNewLinesAfter(1)
                                        );
                                    }
                                }
                            }
                        }
                    }
                } elseif(pathinfo($filePath, PATHINFO_EXTENSION) == 'sql') {
                    $sqlStatement = file_get_contents($filePath);

                    if($this->model->db->query($sqlStatement)) {
                        output()->write(
                            (new Format())
                                ->setContextualClass(Format::SUCCESS)
                                ->setString(language()->getLine('CLI_MIGRATION_SQL_STATEMENT_EXEC_SUCCESS'))
                                ->setNewLinesAfter(1)
                        );
                    } else {
                        output()->write(
                            (new Format())
                                ->setContextualClass(Format::DANGER)
                                ->setString(language()->getLine('CLI_MIGRATION_SQL_STATEMENT_EXEC_FAILED', [$this->model->db->getErrors()]))
                                ->setNewLinesAfter(1)
                        );
                    }
                }

                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Migrate::execute
     */
    public function execute()
    {
        if($this->optionFilename) {
            $this->run($this->optionFilename);
        }
    }
}