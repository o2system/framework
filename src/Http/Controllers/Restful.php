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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Psr\Http\Header\ResponseFieldInterface;
use O2System\Framework\Http\Controller;
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Framework\Models\Sql\Model;
use O2System\Security\Form\Validator;
use O2System\Spl\Exceptions\Logic\OutOfRangeException;

/**
 * Class Restful
 *
 * @package O2System\Framework\Http\Controllers
 */
class Restful extends Controller
{
    /**
     * Push headers flag
     *
     * Used for push default headers by controller.
     * Set to FALSE if you want to set default headers on the web-server configuration.
     *
     * APACHE via .htaccess
     * IIS via .webconfig
     * Nginx via config
     *
     * @type string
     */
    protected $pushDefaultHeaders = false;

    /**
     * Access-Control-Allow-Origin
     *
     * Used for indicates whether a resource can be shared based by
     * returning the value of the Origin request header, "*", or "null" in the response.
     *
     * @type string
     */
    protected $accessControlAllowOrigin = '*';

    /**
     * Access-Control-Allow-Credentials
     *
     * Used for indicates whether the response to request can be exposed when the omit credentials flag is unset.
     * When part of the response to a preflight request it indicates that the actual request can include user
     * credentials.
     *
     * @type bool
     */
    protected $accessControlAllowCredentials = true;

    /**
     * Access-Control-Method
     *
     * @var string
     */
    protected $accessControlMethod = 'GET';

    /**
     * Access-Control-Params
     *
     * @var array
     */
    protected $accessControlParams = [];

    /**
     * Access-Control-Allow-Methods
     *
     * Used for indicates, as part of the response to a preflight request,
     * which methods can be used during the actual request.
     *
     * @type array
     */
    protected $accessControlAllowMethods = [
        'GET', // common request
        'POST', // used for create, update request
        'PUT', // used for upload files request
        'PATCH', // used for action request
        'DELETE', // used for delete request
        'OPTIONS', // used for preflight request
    ];

    /**
     * Access-Control-Allow-Headers
     *
     * Used for indicates, as part of the response to a preflight request,
     * which header field names can be used during the actual request.
     *
     * @type array
     */
    protected $accessControlAllowHeaders = [
        'Origin',
        'Access-Control-Request-Method',
        'Access-Control-Request-Headers',
        'X-Api-Authenticate', // API-Authenticate: api_key="xxx", api_secret="xxx", api_signature="xxx"
        'X-Api-Token',
        'X-Web-Token', // X-Web-Token: xxx (json-web-token)
        'X-Csrf-Token',
        'X-Xss-Token',
        'X-Request-ID',
        'X-Requested-With',
        'X-Requested-Result',
    ];

    /**
     * Access-Control-Allow-Headers
     *
     * Used for indicates, as part of the response to a preflight request,
     * which header field names can be used during the actual request.
     *
     * @type array
     */
    protected $accessControlAllowContentTypes = [
        'text/html',
        'application/json',
        'application/xml',
    ];

    /**
     * Access-Control-Max-Age
     *
     * Used for indicates how long the results of a preflight request can be cached in a preflight result cache
     *
     * @type int
     */
    protected $accessControlMaxAge = 86400;

    /**
     * Restful::$ajaxOnly
     *
     * @var bool
     */
    protected $ajaxOnly = false;

    /**
     * Restful::$model
     *
     * @var \O2System\Framework\Models\Sql\Model|\O2System\Framework\Models\NoSql\Model|\O2System\Framework\Models\Files\Model
     */
    public $model;

    /**
     * Restful::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [];

    /**
     * Restful::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [];

    /**
     * Restful::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [];

    /**
     * Restful::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [];

    /**
     * Restful::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [];

    /**
     * Restful::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [];

    /**
     * Restful::$actionValidationRules
     *
     * @var array
     */
    public $actionValidationRules = [];

    /**
     * Restful::$actionValidationCustomErrors
     *
     * @var array
     */
    public $actionValidationCustomErrors = [];

    /**
     * Restful::$fillableColumns
     *
     * @var array
     */
    public $fillableColumns = [];

    /**
     * Restful::$dataTableColumns
     *
     * @var array
     */
    public $dataTableColumns = [];

    /**
     * Restful::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [];

    // ------------------------------------------------------------------------

    /**
     * Restful::__construct
     */
    public function __construct()
    {
        if (services()->has('presenter')) {
            presenter()->setTheme(false);
        }

        if (is_ajax()) {
            output()->setContentType('application/json');
        } elseif ($this->ajaxOnly === false) {
            output()->setContentType('application/json');
        } else {
            output()->setContentType('text/html');
        }

        if ($contentType = input()->server('HTTP_X_REQUESTED_CONTENT_TYPE')) {
            if (in_array($contentType, $this->accessControlAllowContentTypes)) {
                output()->setContentType($contentType);
            }
        }

        if ($this->pushDefaultHeaders) {

            $origin = input()->server('HTTP_ORIGIN');

            /**
             * Prepare for preflight modern browser request
             *
             * Since some server cannot use 'Access-Control-Allow-Origin: *'
             * the Access-Control-Allow-Origin will be defined based on requested origin
             */
            if ($this->accessControlAllowOrigin === '*') {
                output()->addHeader(ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, $origin);
            }

            // Set response access control allowed credentials
            if ($this->accessControlAllowCredentials === false) {
                output()->addHeader(ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS, 'false');
            }

            // Set response access control allowed methods header
            if (count($this->accessControlAllowMethods)) {
                output()->addHeader(
                    ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS,
                    implode(', ', $this->accessControlAllowMethods)
                );
            }

            // Set response access control allowed headers header
            if (count($this->accessControlAllowHeaders)) {
                output()->addHeader(
                    ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS,
                    implode(', ', $this->accessControlAllowHeaders)
                );
            }

            // Set response access control allowed content types header
            if (count($this->accessControlAllowContentTypes)) {
                output()->addHeader(
                    ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_CONTENT_TYPES,
                    implode(', ', $this->accessControlAllowContentTypes)
                );
            }

            // Set response access control max age header
            if ($this->accessControlMaxAge > 0) {
                output()->addHeader(ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_MAX_AGE,
                    $this->accessControlMaxAge);
            }
        }

        if (input()->server('REQUEST_METHOD') === 'OPTIONS') {
            exit(EXIT_SUCCESS);
        } elseif ( ! in_array(input()->server('REQUEST_METHOD'), $this->accessControlAllowMethods)) {
            $this->sendError(405);
        } elseif (count($this->accessControlParams)) {
            if ($this->accessControlMethod === 'GET') {
                if (empty($_GET)) {
                    $this->sendError(400);
                }
            } elseif ($this->accessControlMethod === 'POST') {
                if (empty($_POST)) {
                    $this->sendError(400);
                }
            } elseif (in_array($this->accessControlMethod, ['GETPOST', 'POSTGET'])) {
                if (empty($_REQUEST)) {
                    $this->sendError(400);
                }
            }
        }

        if (empty($this->model)) {
            $controllerClassName = get_called_class();
            $modelClassName = str_replace('Controllers', 'Models', $controllerClassName);

            if (class_exists($modelClassName)) {
                $this->model = new $modelClassName();
            }
        } elseif (class_exists($this->model)) {
            $this->model = new $this->model();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::index
     */
    public function index()
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            if ($page = input()->get('page')) {
                $this->model->withPaging($page);
                unset($_GET[ 'page' ]);
            }

            if ($limit = input()->get('limit')) {
                $this->model->qb->limit($limit);
                unset($_GET[ 'limit' ]);
            } else {
                $limit = null;
            }

            if ($search = input()->get('search')) {
                if ( ! empty($this->searchableColumns)) {
                    foreach ($this->searchableColumns as $column) {
                        $this->model->qb->orLike($column, $search);
                    }
                }

                unset($_GET[ 'search' ]);
            }

            if (empty($this->getValidationRules)) {
                if ($get = input()->get()) {
                    if (false !== ($result = $this->model->findWhere($get->getArrayCopy(), $limit))) {
                        if ($result->count()) {
                            $this->sendPayload($result);
                        } else {
                            $this->sendError(204);
                        }
                    } else {
                        $this->sendError(204);
                    }
                } elseif (false !== ($result = $this->model->all())) {
                    $this->sendPayload($result);
                } else {
                    $this->sendError(204);
                }
            } elseif ($get = input()->get()) {
                $get->validation($this->getValidationRules, $this->getValidationCustomErrors);

                if ( ! $get->validate()) {
                    $this->sendError(400, implode(', ', $get->validator->getErrors()));
                }

                $conditions = [];

                foreach ($this->getValidationRules as $field => $rule) {
                    $conditions[ $field ] = $get->offsetGet($field);
                }

                if (false !== ($result = $this->model->findWhere($conditions, $limit))) {
                    if ($result->count()) {
                        $this->sendPayload($result);
                    } else {
                        $this->sendError(204);
                    }
                } else {
                    $this->sendError(204);
                }
            } elseif (false !== ($result = $this->model->all())) {
                $this->sendPayload($result);
            } else {
                $this->sendError(204);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::datatable
     */
    public function datatable()
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            $hasAction = false;
            if ($request = input()->request()) {
                // Start as limit
                $this->model->qb->limit($request[ 'start' ]);

                // Length as offset
                $this->model->qb->offset($request[ 'length' ]);

                // Set ordering
                if ( ! empty($request[ 'order' ])) {
                    foreach ($request[ 'order' ] as $dt => $order) {
                        $field = $request[ 'columns' ][ $order[ 'column' ] ][ 'data' ];
                        $this->model->qb->orderBy($field, strtoupper($order[ 'dir' ]));
                    }
                }

                $this->model->visibleColumns = [];

                foreach ($request[ 'columns' ] as $dt => $column) {
                    if ($column[ 'data' ] === 'action') {
                        $this->model->appendColumns[] = 'action';

                        continue;
                    }

                    if ($column[ 'searchable' ]) {
                        if ($dt == 0) {
                            if ( ! empty($column[ 'search' ][ 'value' ])) {
                                $this->model->qb->like($column[ 'data' ], $column[ 'search' ][ 'value' ]);

                                if ( ! empty($request[ 'search' ][ 'value' ])) {
                                    $this->model->qb->orLike($column[ 'data' ], $request[ 'search' ][ 'value' ]);
                                }
                            } elseif ( ! empty($request[ 'search' ][ 'value' ])) {
                                $this->model->qb->like($column[ 'data' ], $request[ 'search' ][ 'value' ]);
                            }
                        } else {
                            if ( ! empty($column[ 'search' ][ 'value' ])) {
                                $this->model->qb->orLike($column[ 'data' ], $column[ 'search' ][ 'value' ]);
                            }

                            if ( ! empty($request[ 'search' ][ 'value' ])) {
                                $this->model->qb->orLike($column[ 'data' ], $request[ 'search' ][ 'value' ]);
                            }
                        }
                    }

                    $this->model->visibleColumns[] = $column[ 'data' ];
                }
            }

            $this->model->rebuildRowCallback(function ($row) {
                $row->DT_RowId = 'datatable-row-' . $row->id;
            });

            if (false !== ($result = $this->model->all())) {
                output()->sendPayload([
                    'draw'            => input()->request('draw'),
                    'recordsTotal'    => $result->info->num_total,
                    'recordsFiltered' => $result->info->num_founds,
                    'data'            => $result->toArray(),
                ]);
            } else {
                $this->sendError(204);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::create
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function create()
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($post = input()->post()) {
                if (count($this->createValidationRules)) {
                    $post->validation($this->createValidationRules, $this->createValidationCustomErrors);
                    if ( ! $post->validate()) {
                        $this->sendError(400, $post->validator->getErrors());
                    }
                }

                $data = $post->getArrayCopy();
                if (count($this->createValidationRules)) {
                    foreach ($this->createValidationRules as $field => $rule) {
                        if ($post->offsetExists($field)) {
                            $data[ $field ] = $post->offsetGet($field);
                        }
                    }
                }

                if (count($this->fillableColumns)) {
                    foreach ($this->fillableColumns as $column) {
                        if ($post->offsetExists($column)) {
                            $data[ $column ] = $post->offsetGet($column);
                        }
                    }
                }

                if (count($data)) {
                    $data[ 'record_create_timestamp' ] = $data[ 'record_update_timestamp' ] = timestamp();

                    if (isset($GLOBALS[ 'account' ][ 'id' ])) {
                        $data[ 'record_create_user' ] = $data[ 'record_update_user' ] = globals()->account->id;
                    }

                    if ($this->model->insert($data)) {
                        $data[ 'id' ] = $this->model->db->getLastInsertId();
                        $this->sendPayload([
                            'code' => 200,
                            'Successful insert request',
                            'data' => $data,
                        ]);
                    } else {
                        $this->sendError(501, 'Failed update request');
                    }
                } else {
                    $this->sendError(400, 'Post parameters cannot be empty!');
                }
            } else {
                $this->sendError(400);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::update
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function update()
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($post = input()->post()) {
                $conditions = [];

                if (empty($this->updateValidationRules)) {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if ($post->offsetExists($primaryKey)) {
                            $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                        }

                        $this->updateValidationRules[ $primaryKey ] = 'required';
                        $this->updateValidationCustomErrors[ $primaryKey ] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    } else {
                        foreach ($this->model->primaryKeys as $primaryKey) {
                            if ($post->offsetExists($primaryKey)) {
                                $conditions[ $primaryKey ] = $post->offsetGet($primaryKey);
                            }

                            $this->updateValidationRules[ $primaryKey ] = 'required';
                            $this->updateValidationCustomErrors[ $primaryKey ] = [
                                'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                            ];
                        }
                    }
                } else {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;

                        if ($post->offsetExists($primaryKey)) {
                            $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                        }
                    } else {
                        foreach ($this->model->primaryKeys as $primaryKey) {
                            if ($post->offsetExists($primaryKey)) {
                                $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                            }
                        }
                    }
                }

                if (count($this->updateValidationRules)) {
                    $post->validation($this->updateValidationRules, $this->updateValidationCustomErrors);
                }

                if ( ! $post->validate()) {
                    $this->sendError(400, implode(', ', $post->validator->getErrors()));
                }

                $data = $post->getArrayCopy();
                if (count($this->updateValidationRules)) {
                    foreach ($this->updateValidationRules as $field => $rule) {
                        if ($post->offsetExists($field)) {
                            $data[ $field ] = $post->offsetGet($field);
                        }
                    }
                }

                if (count($this->fillableColumns)) {
                    foreach ($this->fillableColumns as $column) {
                        if ($post->offsetExists($column)) {
                            $data[ $column ] = $post->offsetGet($column);
                        }
                    }
                }

                if (count($data)) {
                    $data[ 'record_update_timestamp' ] = timestamp();

                    if (isset($GLOBALS[ 'account' ][ 'id' ])) {
                        $data[ 'record_update_user' ] = globals()->account->id;
                    }

                    if (empty($conditions)) {
                        $this->sendError(501, 'Unavailable primary keys data');
                    }

                    if ($this->model->update($data, $conditions)) {
                        $this->sendError(200, 'Successful update request');
                    } else {
                        $this->sendError(501, 'Failed update request');
                    }
                } else {
                    $this->sendError(400, 'Post parameters cannot be empty!');
                }
            } else {
                $this->sendError(400);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::delete
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete($id = null)
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready!');
            }

            if ($post = input()->post()) {
                if (empty($this->actionValidationRules)) {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if ($post->offsetExists($primaryKey)) {
                            $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                        }

                        $this->actionValidationRules[ $primaryKey ] = 'required';
                        $this->actionValidationCustomErrors[ $primaryKey ] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    } else {
                        foreach ($this->model->primaryKeys as $primaryKey) {
                            $conditions[ $primaryKey ] = $post->offsetGet($primaryKey);

                            $this->actionValidationRules[ $primaryKey ] = 'required';
                            $this->actionValidationCustomErrors[ $primaryKey ] = [
                                'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                            ];
                        }
                    }
                } else {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if ($post->offsetExists($primaryKey)) {
                            $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                        }
                    } else {
                        foreach ($this->model->primaryKeys as $primaryKey) {
                            if ($post->offsetExists($primaryKey)) {
                                $conditions[ $primaryKey ] = $post->offsetGet($primaryKey);
                            }
                        }
                    }
                }

                if (count($this->actionValidationRules)) {
                    $post->validation($this->actionValidationRules, $this->actionValidationCustomErrors);
                }

                if (empty($conditions)) {
                    $this->sendError(501, 'Unavailable primary keys data');
                }

                if ( ! $post->validate()) {
                    $this->sendError(400, implode(', ', $post->validator->getErrors()));
                }

                if ($result = $this->model->findWhere($conditions)) {
                    if ($result instanceof Row) {
                        if ($row->delete()) {
                            $this->sendError(200);
                        } else {
                            $this->sendError(501);
                        }
                    } elseif ($result instanceof Result) {
                        foreach ($result as $row) {
                            $row->delete();
                        }

                        $this->sendError(200);
                    }
                } else {
                    $this->sendError(404, 'Data not found!');
                }
            } elseif (input()->server('REQUEST_METHOD') === 'DELETE') {
                $params = func_get_args();
                $validator = new Validator();

                if (empty($this->actionValidationRules)) {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if (count($params)) {
                            $conditions = [$primaryKey => reset($params)];
                        }

                        $this->actionValidationRules[ $primaryKey ] = 'required';
                        $this->actionValidationCustomErrors[ $primaryKey ] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    } else {
                        foreach ($this->model->primaryKeys as $key => $primaryKey) {
                            if (isset($params[ $key ])) {
                                $conditions[ $primaryKey ] = $params[ $key ];
                            }

                            $this->actionValidationRules[ $primaryKey ] = 'required';
                            $this->actionValidationCustomErrors[ $primaryKey ] = [
                                'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                            ];
                        }
                    }
                } else {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if (count($params)) {
                            $conditions = [$primaryKey => reset($params)];
                        }
                    } else {
                        foreach ($this->model->primaryKeys as $key => $primaryKey) {
                            if (isset($params[ $key ])) {
                                $conditions[ $primaryKey ] = $params[ $key ];
                            }
                        }
                    }
                }

                if (count($this->actionValidationRules)) {
                    $validator->setRules($this->actionValidationRules, $this->actionValidationCustomErrors);
                }

                if (empty($conditions)) {
                    $this->sendError(501, 'Unavailable primary keys data');
                }

                if ( ! $validator->validate($conditions)) {
                    $this->sendError(400, implode(', ', $validator->getErrors()));
                }

                if ($result = $this->model->findWhere($conditions)) {
                    if ($result instanceof Row) {
                        if ($row->delete()) {
                            $this->sendError(200);
                        } else {
                            $this->sendError(501);
                        }
                    } elseif ($result instanceof Result) {
                        foreach ($result as $row) {
                            $row->delete();
                        }

                        $this->sendError(200);
                    }
                } else {
                    $this->sendError(404, 'Data not found!');
                }
            } else {
                $this->sendError(400);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::updateRecordStatus
     *
     * @param array  $params
     * @param string $method
     */
    private function updateRecordStatus(array $params, $method)
    {
        if (empty($this->model)) {
            output()->sendError(204);
        } else {
            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($post = input()->post()) {
                if (empty($this->actionValidationRules)) {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if ($post->offsetExists($primaryKey)) {
                            $conditions = [$primaryKey => $post->offsetGet($primaryKey)];
                        }

                        $this->actionValidationRules[ $primaryKey ] = 'required';
                        $this->actionValidationCustomErrors[ $primaryKey ] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    } else {
                        foreach ($this->model->primaryKeys as $primaryKey) {
                            if ($post->offsetExists($primaryKey)) {
                                $conditions[ $primaryKey ] = $post->offsetGet($primaryKey);
                            }

                            $this->actionValidationRules[ $primaryKey ] = 'required';
                            $this->actionValidationCustomErrors[ $primaryKey ] = [
                                'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                            ];
                        }
                    }
                }

                if (count($this->actionValidationRules)) {
                    $post->validation($this->actionValidationRules, $this->actionValidationCustomErrors);
                }

                if (empty($conditions)) {
                    $this->sendError(501, 'Unavailable primary keys data');
                }

                if ( ! $post->validate()) {
                    $this->sendError(400, implode(', ', $post->validator->getErrors()));
                }

                if ($result = $this->model->findWhere($conditions)) {
                    if ($result instanceof Row) {
                        if ($row->{$method}()) {
                            $this->sendError(200);
                        } else {
                            $this->sendError(501);
                        }
                    } elseif ($result instanceof Result) {
                        foreach ($result as $row) {
                            $row->{$method}();
                        }

                        $this->sendError(200);
                    }
                } else {
                    $this->sendError(404, 'Data not found!');
                }
            } elseif (input()->server('REQUEST_METHOD') === 'PATCH') {
                $validator = new Validator();

                if (empty($this->actionValidationRules)) {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if (count($params)) {
                            $conditions = [$primaryKey => reset($params)];
                        }

                        $this->actionValidationRules[ $primaryKey ] = 'required';
                        $this->actionValidationCustomErrors[ $primaryKey ] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    } else {
                        foreach ($this->model->primaryKeys as $key => $primaryKey) {
                            if (isset($params[ $key ])) {
                                $conditions[ $primaryKey ] = $params[ $key ];
                            }

                            $this->actionValidationRules[ $primaryKey ] = 'required';
                            $this->actionValidationCustomErrors[ $primaryKey ] = [
                                'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                            ];
                        }
                    }
                } else {
                    if (empty($this->model->primaryKeys)) {
                        $primaryKey = empty($this->model->primaryKey) ? 'id' : $this->model->primaryKey;
                        if (count($params)) {
                            $conditions = [$primaryKey => reset($params)];
                        }
                    } else {
                        foreach ($this->model->primaryKeys as $key => $primaryKey) {
                            if (isset($params[ $key ])) {
                                $conditions[ $primaryKey ] = $params[ $key ];
                            }
                        }
                    }
                }

                if (count($this->actionValidationRules)) {
                    $validator->setRules($this->actionValidationRules, $this->actionValidationCustomErrors);
                }

                if (empty($conditions)) {
                    $this->sendError(501, 'Unavailable primary keys data');
                }

                if ( ! $validator->validate($conditions)) {
                    $this->sendError(400, implode(', ', $validator->getErrors()));
                }

                if ( ! $this->model instanceof Model) {
                    $this->sendError(503, 'Model is not ready!');
                }

                if ($result = $this->model->findWhere($conditions)) {
                    if ($result instanceof Row) {
                        if ($row->{$method}()) {
                            $this->sendError(200);
                        } else {
                            $this->sendError(501);
                        }
                    } elseif ($result instanceof Result) {
                        foreach ($result as $row) {
                            $row->{$method}();
                        }

                        $this->sendError(200);
                    }
                } else {
                    $this->sendError(404, 'Data not found!');
                }
            } else {
                $this->sendError(400);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::publish
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function publish($id = null)
    {
        $this->updateRecordStatus(func_get_args(), 'publish');
    }

// ------------------------------------------------------------------------

    /**
     * Restful::unpublish
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function unpublish($id = null)
    {
        $this->updateRecordStatus(func_get_args(), 'unpublish');
    }

// ------------------------------------------------------------------------

    /**
     * Restful::archive
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public
    function archive(
        $id = null
    ) {
        $this->updateRecordStatus(func_get_args(), 'archive');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::lock
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function lock($id = null)
    {
        $this->updateRecordStatus(func_get_args(), 'lock');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::softDelete
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function softDelete($id = null)
    {
        $this->updateRecordStatus(func_get_args(), 'softDelete');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::draft
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function draft($id = null)
    {
        $this->updateRecordStatus(func_get_args(), 'draft');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::sendError
     *
     * @param int         $code
     * @param string|null $message
     */
    public function sendError($code, $message = null)
    {
        if ($this->ajaxOnly === false) {
            output()->setContentType('application/json');
        }

        if (is_array($code)) {
            if (is_numeric(key($code))) {
                $message = reset($code);
                $code = key($code);
            } elseif (isset($code[ 'code' ])) {
                $code = $code[ 'code' ];
                $message = $code[ 'message' ];
            }
        }

        output()->sendError($code, $message);
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::sendPayload
     *
     * @param mixed $data        The payload data to-be send.
     * @param bool  $longPooling Long pooling flag mode.
     *
     * @throws \Exception
     */
    public function sendPayload($data, $longPooling = false)
    {
        if ($longPooling === false) {
            if ($this->ajaxOnly) {
                if (is_ajax()) {
                    output()->send($data);
                } else {
                    output()->sendError(403);
                }
            } else {
                output()->send($data);
            }
        } elseif (is_ajax()) {
            /**
             * Server-side file.
             * This file is an infinitive loop. Seriously.
             * It gets the cache created timestamp, checks if this is larger than the timestamp of the
             * AJAX-submitted timestamp (time of last ajax request), and if so, it sends back a JSON with the data from
             * data.txt (and a timestamp). If not, it waits for one seconds and then start the next while step.
             *
             * Note: This returns a JSON, containing the content of data.txt and the timestamp of the last data.txt change.
             * This timestamp is used by the client's JavaScript for the next request, so THIS server-side script here only
             * serves new content after the last file change. Sounds weird, but try it out, you'll get into it really fast!
             */

            // set php runtime to unlimited
            set_time_limit(0);

            $longPoolingCacheKey = 'long-pooling-' . session()->get('id');
            $longPoolingCacheData = null;

            if ( ! cache()->hasItem($longPoolingCacheKey)) {
                cache()->save(new Item($longPoolingCacheKey, $data));
            }

            // main loop
            while (true) {
                // if ajax request has send a timestamp, then $lastCallTimestamp = timestamp, else $last_call = null
                $lastCallTimestamp = (int)input()->getPost('last_call_timestamp');

                // PHP caches file data, like requesting the size of a file, by default. clearstatcache() clears that cache
                clearstatcache();

                if (cache()->hasItem($longPoolingCacheKey)) {
                    $longPoolingCacheData = cache()->getItem($longPoolingCacheKey);
                }

                // get timestamp of when file has been changed the last time
                $longPoolingCacheMetadata = $longPoolingCacheData->getMetadata();

                // if no timestamp delivered via ajax or data.txt has been changed SINCE last ajax timestamp
                if ($lastCallTimestamp == null || $longPoolingCacheMetadata[ 'ctime' ] > $lastCallTimestamp) {
                    output()->send([
                        'timestamp' => $longPoolingCacheMetadata,
                        'data'      => $data,
                    ]);
                } else {
                    // wait for 1 sec (not very sexy as this blocks the PHP/Apache process, but that's how it goes)
                    sleep(1);
                    continue;
                }
            }
        } else {
            output()->sendError(501);
        }
    }
}