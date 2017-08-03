<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 6/13/2017
 * Time: 6:40 PM
 */

namespace O2System\Framework\Libraries\Acl\Sso;

use O2System\Framework\Http\Message\Uri;
use O2System\Curl;

/**
 * Class Broker
 *
 * The broker lives on the website visited by the user. The broken doesn't have any user credentials stored. Instead it
 * will talk to the SSO server in name of the user, verifying credentials and getting user information.
 *
 * @package O2System\Framework\Libraries\Acl\Sso
 */
class Broker
{
    protected $uri;
    protected $key;
    protected $secret;

    /**
     * Broker::__construct
     *
     * @param $broker
     * @param $secret
     */
    public function __construct( Uri $uri, $key, $secret )
    {
        $this->uri = $uri;
        $this->key = $key;
        $this->secret = $secret;
    }

    public function signIn()
    {
        ob_start();



        $content = ob_get_contents();
        ob_end_clean();

        $content = file_get_contents('http://api.o2system5.dev/auth/cookie/?domain=yubimall.dev');
        print_out( $content );
        /* STEP 1. let’s create a cookie file */
        $ckfile = tempnam( "/tmp", "CURLCOOKIE" );
        /* STEP 2. visit the homepage to set the cookie properly */
        $ch = curl_init( $this->uri->addPath( 'cookie' )->__toString() );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $ckfile );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );

        /* STEP 3. visit cookiepage.php */
        $ch = curl_init( $this->uri->addPath( 'cookie' )->__toString() );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $ckfile );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );


        $fh = fopen( $this->uri->addPath( 'cookie' )->__toString(), 'r' );
        $details = stream_get_contents( $fh );

        print_out( $details );
        $session = file_get_contents( $this->uri->addPath( 'cookie' )->__toString() );
        print_out( $session );
        $request = new Curl\Request();
        $request->setUserAgent( $_SERVER[ 'HTTP_USER_AGENT' ] );
        $request->setUri( $this->uri->addPath( 'sign-in' ) );
        $result = $request->post( [
            'broker' => [
                'key'    => $this->key,
                'secret' => $this->secret,
            ],
        ] );

        print_out( $result );
    }
}