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

/*
|--------------------------------------------------------------------------
| Framework Name and Version
|--------------------------------------------------------------------------
*/
define( 'FRAMEWORK_NAME', 'O2System PHP Framework' );
define( 'FRAMEWORK_VERSION', '5.0.0-dev' );

/*
|--------------------------------------------------------------------------
| Timing Constants
|--------------------------------------------------------------------------
|
| Provide simple ways to work with the myriad of PHP functions that
| require information to be in seconds.
*/
defined( 'SECOND' ) || define( 'SECOND', 1 );
defined( 'MINUTE' ) || define( 'MINUTE', 60 );
defined( 'HOUR' ) || define( 'HOUR', 3600 );
defined( 'DAY' ) || define( 'DAY', 86400 );
defined( 'WEEK' ) || define( 'WEEK', 604800 );
defined( 'MONTH' ) || define( 'MONTH', 2592000 );
defined( 'YEAR' ) || define( 'YEAR', 31536000 );

/*
| -------------------------------------------------------------------
|  PHP File Extension Constant
| -------------------------------------------------------------------
*/
define( '__EXT__', '.php' );

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define( 'FILE_READ_MODE', 0644 );
define( 'FILE_WRITE_MODE', 0666 );
define( 'DIR_READ_MODE', 0755 );
define( 'DIR_WRITE_MODE', 0755 );

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined( 'EXIT_SUCCESS' ) || define( 'EXIT_SUCCESS', 0 ); // no errors
defined( 'EXIT_ERROR' ) || define( 'EXIT_ERROR', 1 ); // generic error
defined( 'EXIT_CONFIG' ) || define( 'EXIT_CONFIG', 3 ); // configuration error
defined( 'EXIT_UNKNOWN_FILE' ) || define( 'EXIT_UNKNOWN_FILE', 4 ); // file not found
defined( 'EXIT_UNKNOWN_CLASS' ) || define( 'EXIT_UNKNOWN_CLASS', 5 ); // unknown class
defined( 'EXIT_UNKNOWN_METHOD' ) || define( 'EXIT_UNKNOWN_METHOD', 6 ); // unknown class member
defined( 'EXIT_USER_INPUT' ) || define( 'EXIT_USER_INPUT', 7 ); // invalid user input
defined( 'EXIT_DATABASE' ) || define( 'EXIT_DATABASE', 8 ); // database error
defined( 'EXIT__AUTO_MIN' ) || define( 'EXIT__AUTO_MIN', 9 ); // lowest automatically-assigned error code
defined( 'EXIT__AUTO_MAX' ) || define( 'EXIT__AUTO_MAX', 125 ); // highest automatically-assigned error code

/*
| -------------------------------------------------------------------
| Logger Constants
| -------------------------------------------------------------------
*/
define( 'LOGGER_DISABLED', 0 ); // disabled all log writing
define( 'LOGGER_INFO', 1 ); // write log marked as INFO
define( 'LOGGER_ERROR', 2 ); // write log marked as ERROR
define( 'LOGGER_DEBUG', 3 ); // write log marked as DEBUG
define( 'LOGGER_NOTICE', 4 ); // write log marked as NOTICE
define( 'LOGGER_WARNING', 5 ); // write log marked as WARNING
define( 'LOGGER_ALERT', 6 ); // write log marked as ALERT
define( 'LOGGER_EMERGENCY', 7 ); // write log marked as EMERGENCY
define( 'LOGGER_CRITICAL', 8 ); // write log marked as CRITICAL
define( 'LOGGER_ALL', 9 ); // enabled all log writing