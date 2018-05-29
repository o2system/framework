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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Http\Controller;
use O2System\Psr\Http\Header\ResponseFieldInterface;

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
        //'PUT', // used for upload files request
        //'DELETE', // used for delete request
        'OPTIONS', // used for preflight request
    ];

    /**
     * Access-Control-Allow-Headers
     *
     * Used for indicates, as part of the response to a preflight request,
     * which header field names can be used during the actual request.
     *
     * @type int
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

    // ------------------------------------------------------------------------

    protected $ajaxOnly = false;

    /**
     * Restful::__construct
     */
    public function __construct()
    {
        if (o2system()->hasService('presenter')) {
            presenter()->theme->set(false);
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
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::index
     */
    public function index()
    {
        output()->sendError(204);
    }

    // ------------------------------------------------------------------------

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