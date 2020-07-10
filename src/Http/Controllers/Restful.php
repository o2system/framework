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
use O2System\Spl\DataStructures\SplArrayStorage;
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
        } elseif (!in_array(input()->server('REQUEST_METHOD'), $this->accessControlAllowMethods)) {
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
            if (!$this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            if (!$this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            if ($page = input()->get('page')) {
                $this->model->withPaging($page);
                unset($_GET['page']);
            }

            if ($limit = input()->get('limit')) {
                $this->model->qb->limit($limit);
                unset($_GET['limit']);
            } else {
                $limit = null;
            }

            if ($search = input()->get('search')) {
                if (!empty($this->searchableColumns)) {
                    foreach ($this->searchableColumns as $column) {
                        $this->model->qb->orLike($column, $search);
                    }
                }

                unset($_GET['search']);
            }

            if (empty($this->getValidationRules)) {
                if ($get = input()->get()) {
                    if ($scopes = $get->offsetGet('scopes')) {
                        $get->offsetUnset('scopes');
                        $scopes = explode(',', $scopes);
                        $scopes = array_map('trim', $scopes);
                        $scopes = array_filter($scopes);

                        $this->model->appendColumns = $scopes;
                    }

                    if (false !== ($result = $this->model->findWhere($get->getArrayCopy(), $limit))) {
                        if ($result->count()) {
                            if ($get->offsetExists('id')) {
                                $result = $result->first();
                            }

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
                if ($scopes = $get->offsetGet('scopes')) {
                    $get->offsetUnset('scopes');
                    $scopes = explode(',', $scopes);
                    $scopes = array_map('trim', $scopes);
                    $scopes = array_filter($scopes);

                    $this->model->appendColumns = $scopes;
                }

                $get->validation($this->getValidationRules, $this->getValidationCustomErrors);

                if (!$get->validate()) {
                    $this->sendError(400, implode(', ', $get->validator->getErrors()));
                }

                if (false !== ($result = $this->model->findWhere($get->getArrayCopy(), $limit))) {
                    if ($result->count()) {
                        if ($get->offsetExists('id')) {
                            $result = $result->first();
                        }

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
            if (!$this->model instanceof Model) {
                $this->sendError(503, 'Model is not exists!');
            }

            $hasAction = false;
            if ($request = input()->request()) {
                // Start as limit
                $this->model->qb->limit($request['start']);

                // Length as offset
                $this->model->qb->offset($request['length']);

                // Set ordering
                if (!empty($request['order'])) {
                    foreach ($request['order'] as $dt => $order) {
                        $field = $request['columns'][$order['column']]['data'];
                        $this->model->qb->orderBy($field, strtoupper($order['dir']));
                    }
                }

                $this->model->visibleColumns = [];

                foreach ($request['columns'] as $dt => $column) {
                    if ($column['data'] === 'action') {
                        $this->model->appendColumns[] = 'action';

                        continue;
                    }

                    if ($column['searchable']) {
                        if ($dt == 0) {
                            if (!empty($column['search']['value'])) {
                                $this->model->qb->like($column['data'], $column['search']['value']);

                                if (!empty($request['search']['value'])) {
                                    $this->model->qb->orLike($column['data'], $request['search']['value']);
                                }
                            } elseif (!empty($request['search']['value'])) {
                                $this->model->qb->like($column['data'], $request['search']['value']);
                            }
                        } else {
                            if (!empty($column['search']['value'])) {
                                $this->model->qb->orLike($column['data'], $column['search']['value']);
                            }

                            if (!empty($request['search']['value'])) {
                                $this->model->qb->orLike($column['data'], $request['search']['value']);
                            }
                        }
                    }

                    $this->model->visibleColumns[] = $column['data'];
                }
            }

            $this->model->rebuildRowCallback(function ($row) {
                $row->DT_RowId = 'datatable-row-' . $row->id;
            });

            if (false !== ($result = $this->model->all())) {
                output()->sendPayload([
                    'draw' => input()->request('draw'),
                    'recordsTotal' => $result->info->num_total,
                    'recordsFiltered' => $result->info->num_founds,
                    'data' => $result->toArray(),
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
            if (!$this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($post = input()->post()) {
                if ($post->count()) {
                    if ($this->model->insert($post)) {
                        $this->sendPayload([
                            'code' => 200,
                            'Successful insert request',
                            'data' => $post,
                        ]);
                    } else {
                        $this->sendError(400, $this->model->getLastErrorMessage());
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
        if (in_array(input()->server('REQUEST_METHOD'), ['PUT', 'POST'])) {
            if (empty($this->model)) {
                output()->sendError(204);
            } else {
                if (!$this->model instanceof Model) {
                    $this->sendError(503, 'Model is not ready');
                }

                if ($post = input()->post()) {
                    if ($post->count()) {
                        if ($this->model->update($post)) {
                            $this->sendPayload([
                                'code' => 200,
                                'Successful update request',
                                'data' => $post,
                            ]);
                        } else {
                            $this->sendError(400, $this->model->getLastErrorMessage());
                        }
                    } else {
                        $this->sendError(400, 'Post parameters cannot be empty!');
                    }
                } else {
                    $this->sendError(400);
                }
            }
        } else {
            $this->sendError(403, 'Invalid request method!');
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
    public function delete($id)
    {
        if (input()->server('REQUEST_METHOD') === 'DELETE') {
            if (empty($this->model)) {
                output()->sendError(204);
            } else {
                if (!$this->model instanceof Model) {
                    $this->sendError(503, 'Model is not ready!');
                }

                $params = func_get_args();

                if(count($params)) {
                    $conditions = [];

                    if(empty($this->model->primaryKeys)) {
                        $conditions[$this->model->primaryKey] = reset($params);
                    } else {
                        foreach($this->model->primaryKeys as $key => $primaryKey) {
                            $conditions[$primaryKey] = $params[$key];
                        }
                    }

                    if($this->model->deleteBy($conditions)) {
                        $this->sendPayload([
                            'code' => 200,
                            'Successful delete data'
                        ]);
                    } else {
                        $this->sendError(400, $this->model->getLastErrorMessage());
                    }
                } else {
                    $this->sendError(400, 'Request parameters cannot be empty!');
                }
            }
        } else {
            $this->sendError(403, 'Invalid request method!');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::updateRecordStatus
     *
     * @param array $params
     * @param string $recordStatus
     * @param string $method
     */
    private function updateRecordStatus(array $primaryKeys, $recordStatus, $method)
    {
        if (input()->server('REQUEST_METHOD') === 'PATCH') {
            if (empty($this->model)) {
                output()->sendError(204);
            } else {
                if (!$this->model instanceof Model) {
                    $this->sendError(503, 'Model is not ready');
                }

                if (count($primaryKeys)) {
                    if ($this->model->updateRecordStatus($primaryKeys, $recordStatus, $method)) {
                        $this->sendPayload([
                            'code' => 200,
                            'Successful ' . $method . ' request',
                        ]);
                    } else {
                        $this->sendError(400, $this->model->getLastErrorMessage());
                    }
                } else {
                    $this->sendError(400, 'Request parameters cannot be empty!');
                }
            }
        } else {
            $this->sendError(403, 'Invalid request method!');
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
    public function publish($id)
    {
        $this->updateRecordStatus(func_get_args(), 'PUBLISH', 'publish');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::unpublish
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function unpublish($id)
    {
        $this->updateRecordStatus(func_get_args(), 'UNPUBLISH', 'unpublish');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::archive
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function archive($id)
    {
        $this->updateRecordStatus(func_get_args(), 'ARCHIVED', 'archive');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::lock
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function lock($id)
    {
        $this->updateRecordStatus(func_get_args(), 'LOCKED', 'lock');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::softDelete
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function softDelete($id)
    {
        $this->updateRecordStatus(func_get_args(), 'DELETED', 'softDelete');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::draft
     *
     * @param int|null $id
     *
     * @throws OutOfRangeException
     */
    public function draft($id)
    {
        $this->updateRecordStatus(func_get_args(), 'DRAFT', 'draft');
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::sendError
     *
     * @param int $code
     * @param string|null $message
     */
    protected function sendError($code, $message = null)
    {
        if ($this->ajaxOnly === false) {
            output()->setContentType('application/json');
        }

        if (is_array($code)) {
            if (is_numeric(key($code))) {
                $message = reset($code);
                $code = key($code);
            } elseif (isset($code['code'])) {
                $code = $code['code'];
                $message = $code['message'];
            }
        }

        output()->sendError($code, $message);
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::sendPayload
     *
     * @param mixed $data The payload data to-be send.
     * @param bool $longPooling Long pooling flag mode.
     *
     * @throws \Exception
     */
    protected function sendPayload($data, $longPooling = false)
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

            if (!cache()->hasItem($longPoolingCacheKey)) {
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
                if ($lastCallTimestamp == null || $longPoolingCacheMetadata['ctime'] > $lastCallTimestamp) {
                    output()->send([
                        'timestamp' => $longPoolingCacheMetadata,
                        'data' => $data,
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