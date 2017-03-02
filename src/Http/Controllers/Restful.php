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

use O2System\Framework\Http\Controller;

/**
 * Class Restful
 *
 * @package O2System\Framework\Http\Controllers
 */
class Restful extends Controller
{
    /**
     * Push Access-Control-Allow-Origin flag
     *
     * Used for push 'Access-Control-Allow-Origin: *' to header
     * If you're set this flag into FALSE you must push it via server configuration
     *
     * APACHE via .htaccess
     * IIS via .webconfig
     * Nginx via config
     *
     * @type string
     */
    protected $isPushAccessControlAllowOrigin = TRUE;

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
    protected $accessControlAllowCredentials = TRUE;

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
        'DELETE', // used for delete request
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
        'API-Authenticate', // API-Authenticate: api_key="xxx", api_secret="xxx", api_signature="xxx"
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
     * @type int
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
     * Access-Control-Last-Polling-Call-Timestamp
     *
     * Used for indicates last long polling call timestamp
     *
     * @type int Time
     */
    protected $accessControlLastPollingCallTimestamp;

    /**
     * Access-Control-Last-Polling-Changed-Timestamp
     *
     * Used for indicates last long polling changed timestamp
     *
     * @type int Time
     */
    protected $accessControlLastPollingChangedTimestamp;

    // ------------------------------------------------------------------------

    /**
     * Restful::__construct
     */
    public function __construct()
    {
        presenter()->setTheme( FALSE );

        if( is_ajax() ) {
            output()->setContentType( 'application/json' );
        } else {
            output()->setContentType( 'text/html' );
        }

        if( $contentType = input()->server('HTTP_X_REQUESTED_CONTENT_TYPE') ) {
            if( in_array( $contentType, $this->accessControlAllowContentTypes )) {
                output()->setContentType($contentType);
            }
        }
    }

    public function index()
    {
        output()->sendError( 204 ) ;
    }
}