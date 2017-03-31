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
use O2System\Psr\Http\Header\ResponseFieldInterface;

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
    protected $pushAccessControlAllowOrigin = true;

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
        presenter()->setTheme( false );

        if ( is_ajax() ) {
            output()->setContentType( 'application/json' );
        } else {
            output()->setContentType( 'text/html' );
        }

        if ( $contentType = input()->server( 'HTTP_X_REQUESTED_CONTENT_TYPE' ) ) {
            if ( in_array( $contentType, $this->accessControlAllowContentTypes ) ) {
                output()->setContentType( $contentType );
            }
        }

        if ( $this->pushAccessControlAllowOrigin ) {

            $origin = input()->server( 'HTTP_ORIGIN' );

            /**
             * Prepare for preflight modern browser request
             *
             * Since some server cannot use 'Access-Control-Allow-Origin: *'
             * the Access-Control-Allow-Origin will be defined based on requested origin
             */
            if ( $this->accessControlAllowOrigin === '*' ) {
                output()->addHeader( ResponseFieldInterface::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS, $origin );
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Restful::index
     */
    public function index()
    {
        output()->sendError( 204 );
    }

    /**
     * Server-side file.
     * This file is an infinitive loop. Seriously.
     * It gets the file data.txt's last-changed timestamp, checks if this is larger than the timestamp of the
     * AJAX-submitted timestamp (time of last ajax request), and if so, it sends back a JSON with the data from
     * data.txt (and a timestamp). If not, it waits for one seconds and then start the next while step.
     *
     * Note: This returns a JSON, containing the content of data.txt and the timestamp of the last data.txt change.
     * This timestamp is used by the client's JavaScript for the next request, so THIS server-side script here only
     * serves new content after the last file change. Sounds weird, but try it out, you'll get into it really fast!
     */
    protected function sendLongPoolingPayload()
    {
        if ( method_exists( $this, 'getLastPollingChangedTimestamp' ) AND method_exists( $this, 'getLastPollingData' ) )
        {
            // set php runtime to unlimited
            set_time_limit( 0 );

            // main loop
            while ( TRUE )
            {
                // if ajax request has send a timestamp, then $last_ajax_call = timestamp, else $last_ajax_call = null
                $this->accessControlLastPollingCallTimestamp = (int) input()->getPost( 'last_call_timestamp' );

                // PHP caches file data, like requesting the size of a file, by default. clearstatcache() clears that cache
                clearstatcache();

                // get timestamp of when file has been changed the last time
                $this->accessControlLastPollingChangedTimestamp = (int) $this->getLastPollingChangedTimestamp();

                // if no timestamp delivered or last polling changed timestamp has been changed SINCE last call timestamp
                if ( $this->accessControlLastPollingCallTimestamp == 0 OR $this->accessControlLastPollingChangedTimestamp > $this->accessControlLastPollingCallTimestamp )
                {
                    // get last polling changed data
                    $data = $this->getLastPollingData();

                    output()->send([
                        'metadata' => [
                            'timestamp' => [
                                'last_call'    => $this->accessControlLastPollingCallTimestamp,
                                'last_changed' => $this->accessControlLastPollingChangedTimestamp,
                            ]
                        ],
                        'data' => $data
                    ]);

                    // leave this loop step
                    break;

                }
                else
                {
                    // wait for 1 sec (not very sexy as this blocks the PHP/Apache process, but that's how it goes)
                    sleep( 1 );
                    continue;
                }
            }
        }
        else
        {
            output()->sendError( 501 );
        }
    }
}