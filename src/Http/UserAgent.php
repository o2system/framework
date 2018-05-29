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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

/**
 * Class UserAgent
 *
 * @package O2System\Framework\Http
 */
class UserAgent
{
    /**
     * List of platforms to compare against current user agent
     *
     * @var array
     */
    public static $platforms = [];

    /**
     * List of browsers to compare against current user agent
     *
     * @var array
     */
    public static $browsers = [];

    /**
     * List of mobile browsers to compare against current user agent
     *
     * @var array
     */
    public static $mobiles = [];

    /**
     * List of robots to compare against current user agent
     *
     * @var array
     */
    public static $robots = [];

    /**
     * Current user-agent
     *
     * @var string
     */
    public $string = null;

    /**
     * Flag for if the user-agent belongs to a browser
     *
     * @var bool
     */
    public $isBrowser = false;

    /**
     * Flag for if the user-agent is a robot
     *
     * @var bool
     */
    public $isRobot = false;

    /**
     * Flag for if the user-agent is a mobile browser
     *
     * @var bool
     */
    public $isMobile = false;

    /**
     * Languages accepted by the current user agent
     *
     * @var array
     */
    public $languages = [];

    /**
     * Character sets accepted by the current user agent
     *
     * @var array
     */
    public $charsets = [];

    /**
     * Current user-agent platform
     *
     * @var string
     */
    public $platform = '';

    /**
     * Current user-agent browser
     *
     * @var string
     */
    public $browser = '';

    /**
     * Current user-agent version
     *
     * @var string
     */
    public $version = '';

    /**
     * Current user-agent mobile name
     *
     * @var string
     */
    public $mobile = '';

    /**
     * Current user-agent device type
     *
     * @type string
     */
    public $device = '';

    /**
     * Current user-agent robot name
     *
     * @var string
     */
    public $robot = '';

    /**
     * HTTP Referer
     *
     * @var    mixed
     */
    public $referer;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * Sets the User Agent and runs the compilation routine
     *
     * @return    void
     */
    public function __construct()
    {
        if (isset($_SERVER[ 'HTTP_USER_AGENT' ])) {
            $this->string = trim($_SERVER[ 'HTTP_USER_AGENT' ]);
        }

        if ($this->string !== null && $this->loadAgentFile()) {
            $this->compileData();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Compile the User Agent Data
     *
     * @return    bool
     */
    protected function loadAgentFile()
    {
        if (($found = is_file(PATH_FRAMEWORK . 'Config/UserAgents.php'))) {
            include(PATH_FRAMEWORK . 'Config/UserAgents.php');
        }

        if ($found !== true) {
            return false;
        }

        $return = false;

        if (isset($platforms)) {
            static::$platforms = $platforms;
            unset($platforms);
            $return = true;
        }

        if (isset($browsers)) {
            static::$browsers = $browsers;
            unset($browsers);
            $return = true;
        }

        if (isset($mobiles)) {
            static::$mobiles = $mobiles;
            unset($mobiles);
            $return = true;
        }

        if (isset($robots)) {
            static::$robots = $robots;
            unset($robots);
            $return = true;
        }

        return $return;
    }

    // --------------------------------------------------------------------

    /**
     * Compile the User Agent Data
     *
     * @return    bool
     */
    protected function compileData()
    {
        $this->setPlatform();

        foreach (['setRobot', 'setBrowser', 'setMobile'] as $function) {
            if ($this->$function() === true) {
                break;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set the Platform
     *
     * @return    bool
     */
    protected function setPlatform()
    {
        if (is_array(static::$platforms) && count(static::$platforms) > 0) {
            foreach (static::$platforms as $key => $val) {
                if (preg_match('|' . preg_quote($key) . '|i', $this->string)) {
                    $this->platform = $val;

                    return true;
                }
            }
        }

        $this->platform = 'Unknown Platform';

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Is Browser
     *
     * @param    string $key
     *
     * @return    bool
     */
    public function isBrowser($key = null)
    {
        if ( ! $this->isBrowser) {
            return false;
        }

        // No need to be specific, it's a browser
        if ($key === null) {
            return true;
        }

        // Check for a specific browser
        return (isset(static::$browsers[ $key ]) && $this->browser === static::$browsers[ $key ]);
    }

    // --------------------------------------------------------------------

    /**
     * Is Robot
     *
     * @param    string $key
     *
     * @return    bool
     */
    public function isRobot($key = null)
    {
        if ( ! $this->isRobot) {
            return false;
        }

        // No need to be specific, it's a robot
        if ($key === null) {
            return true;
        }

        // Check for a specific robot
        return (isset(static::$robots[ $key ]) && $this->robot === static::$robots[ $key ]);
    }

    // --------------------------------------------------------------------

    /**
     * Is Mobile
     *
     * @param    string $key
     *
     * @return    bool
     */
    public function isMobile($key = null)
    {
        if ( ! $this->isMobile) {
            return false;
        }

        // No need to be specific, it's a mobile
        if ($key === null) {
            return true;
        }

        // Check for a specific robot
        return (isset(static::$mobiles[ $key ]) && $this->mobile === static::$mobiles[ $key ]);
    }

    // --------------------------------------------------------------------

    /**
     * Is this a referral from another site?
     *
     * @return    bool
     */
    public function isReferral()
    {
        if ( ! isset($this->referer)) {
            if (empty($_SERVER[ 'HTTP_REFERER' ])) {
                $this->referer = false;
            } else {
                $referer_host = @parse_url($_SERVER[ 'HTTP_REFERER' ], PHP_URL_HOST);
                $own_host = parse_url(o2system()->config->base_url(), PHP_URL_HOST);

                $this->referer = ($referer_host && $referer_host !== $own_host);
            }
        }

        return $this->referer;
    }

    // --------------------------------------------------------------------

    /**
     * Agent String
     *
     * @return    string
     */
    public function agentString()
    {
        return $this->string;
    }

    // --------------------------------------------------------------------

    /**
     * Get Platform
     *
     * @return    string
     */
    public function platform()
    {
        return $this->platform;
    }

    // --------------------------------------------------------------------

    /**
     * Get Browser Name
     *
     * @return    string
     */
    public function browser()
    {
        return $this->browser;
    }

    // --------------------------------------------------------------------

    /**
     * Get the Browser Version
     *
     * @return    string
     */
    public function version()
    {
        return $this->version;
    }

    // --------------------------------------------------------------------

    /**
     * Get The Robot Name
     *
     * @return    string
     */
    public function robot()
    {
        return $this->robot;
    }

    // --------------------------------------------------------------------

    /**
     * Get the Mobile Device
     *
     * @return    string
     */
    public function mobile()
    {
        return $this->mobile;
    }

    // --------------------------------------------------------------------

    /**
     * Get the referrer
     *
     * @return    bool
     */
    public function referrer()
    {
        return empty($_SERVER[ 'HTTP_REFERER' ]) ? '' : trim($_SERVER[ 'HTTP_REFERER' ]);
    }

    // --------------------------------------------------------------------

    /**
     * Test for a particular language
     *
     * @param    string $lang
     *
     * @return    bool
     */
    public function acceptLang($lang = 'en')
    {
        return in_array(strtolower($lang), $this->languages(), true);
    }

    // --------------------------------------------------------------------

    /**
     * Get the accepted languages
     *
     * @return    array
     */
    public function languages()
    {
        if (count($this->languages) === 0) {
            $this->setLanguages();
        }

        return $this->languages;
    }

    // --------------------------------------------------------------------

    /**
     * Set the accepted languages
     *
     * @return    void
     */
    protected function setLanguages()
    {
        if ((count($this->languages) === 0) && ! empty($_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ])) {
            $this->languages = explode(
                ',',
                preg_replace(
                    '/(;\s?q=[0-9\.]+)|\s/i',
                    '',
                    strtolower(trim($_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ]))
                )
            );
        }

        if (count($this->languages) === 0) {
            $this->languages = ['Undefined'];
        }
    }
    // --------------------------------------------------------------------

    /**
     * Test for a particular character set
     *
     * @param    string $charset
     *
     * @return    bool
     */
    public function acceptCharset($charset = 'utf-8')
    {
        return in_array(strtolower($charset), $this->charsets(), true);
    }

    // --------------------------------------------------------------------

    /**
     * Get the accepted Character Sets
     *
     * @return    array
     */
    public function charsets()
    {
        if (count($this->charsets) === 0) {
            $this->setCharsets();
        }

        return $this->charsets;
    }

    // --------------------------------------------------------------------

    /**
     * Set the accepted character sets
     *
     * @return    void
     */
    protected function setCharsets()
    {
        if ((count($this->charsets) === 0) && ! empty($_SERVER[ 'HTTP_ACCEPT_CHARSET' ])) {
            $this->charsets = explode(
                ',',
                preg_replace(
                    '/(;\s?q=.+)|\s/i',
                    '',
                    strtolower(trim($_SERVER[ 'HTTP_ACCEPT_CHARSET' ]))
                )
            );
        }

        if (count($this->charsets) === 0) {
            $this->charsets = ['Undefined'];
        }
    }

    // --------------------------------------------------------------------

    /**
     * Parse a custom user-agent string
     *
     * @param    string $string
     *
     * @return    void
     */
    public function parse($string)
    {
        // Reset values
        $this->isBrowser = false;
        $this->isRobot = false;
        $this->isMobile = false;
        $this->browser = '';
        $this->version = '';
        $this->mobile = '';
        $this->robot = '';

        // Set the new user-agent string and parse it, unless empty
        $this->string = $string;

        if ( ! empty($string)) {
            $this->compileData();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set the Browser
     *
     * @return    bool
     */
    protected function setBrowser()
    {
        if (is_array(static::$browsers) && count(static::$browsers) > 0) {
            foreach (static::$browsers as $key => $val) {
                if (preg_match('|' . $key . '.*?([0-9\.]+)|i', $this->string, $match)) {
                    $this->isBrowser = true;
                    $this->version = $match[ 1 ];
                    $this->browser = $val;
                    $this->setMobile();

                    return true;
                }
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Set the Mobile Device
     *
     * @return    bool
     */
    protected function setMobile()
    {
        if (is_array(static::$mobiles) && count(static::$mobiles) > 0) {
            foreach (static::$mobiles as $key => $val) {
                if (false !== (stripos($this->string, $key))) {
                    $this->isMobile = true;
                    $this->mobile = $val;

                    return true;
                }
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Set the Robot
     *
     * @return    bool
     */
    protected function setRobot()
    {
        if (is_array(static::$robots) && count(static::$robots) > 0) {
            foreach (static::$robots as $key => $val) {
                if (preg_match('|' . preg_quote($key) . '|i', $this->string)) {
                    $this->isRobot = true;
                    $this->robot = $val;
                    $this->setMobile();

                    return true;
                }
            }
        }

        return false;
    }
}