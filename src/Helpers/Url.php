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

if ( ! function_exists('base_url')) {
    function base_url($segments = null, $query = null)
    {
        $uri = (new \O2System\Kernel\Http\Message\Uri())
            ->withSegments(new \O2System\Kernel\Http\Message\Uri\Segments(''))
            ->withQuery('');

        if ($uriConfig = config()->offsetGet('uri')) {
            if ( ! empty($uriConfig[ 'base' ])) {
                $base = (is_https() ? 'https' : 'http') . '://' . str_replace(['http://', 'https://'], '',
                        $uriConfig[ 'base' ]);
                $uri = new \O2System\Kernel\Http\Message\Uri($base);
            }
        }

        if (isset($segments)) {
            $uri = $uri->addSegments($segments);
        }

        if (isset($query)) {
            $uri = $uri->addQuery($query);
        }

        return $uri->__toString();
    }
}

if ( ! function_exists('domain_url')) {
    function domain_url($segments = null, $query = null, $subdomain = null)
    {
        $uri = (new \O2System\Kernel\Http\Message\Uri())
            ->withSubDomain($subdomain)
            ->withSegments(new \O2System\Kernel\Http\Message\Uri\Segments(''))
            ->withQuery('');

        if ($uriConfig = config()->offsetGet('uri')) {
            if ( ! empty($uriConfig[ 'base' ])) {
                $base = (is_https() ? 'https' : 'http') . '://' . str_replace(['http://', 'https://'], '',
                        $uriConfig[ 'base' ]);
                $uri = new \O2System\Kernel\Http\Message\Uri($base);
            }
        }

        if (isset($segments)) {
            $uri = $uri->addSegments($segments);
        }

        if (isset($query)) {
            $uri = $uri->addQuery($query);
        }

        return $uri->__toString();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('current_url')) {

    function current_url($segments = null, $query = null)
    {
        $uri = new \O2System\Kernel\Http\Message\Uri();

        if (isset($segments)) {
            $uri = $uri->addSegments($segments);
        }

        if (isset($query)) {
            $uri = $uri->addQuery($query);
        }

        return $uri->__toString();
    }
}

if ( ! function_exists('assets_url')) {
    /**
     * assets_url
     *
     * @param string $path Uri path.
     *
     * @return string
     */
    function assets_url($path)
    {
        return presenter()->assets->file($path);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('storage_url')) {
    /**
     * assets_url
     *
     * @param string $path Uri path.
     *
     * @return string
     */
    function storage_url($path)
    {
        $urlPath = str_replace(PATH_STORAGE, '', $path);

        return base_url('storage/' . $urlPath);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('images_url')) {
    /**
     * images_url
     *
     * @param string $path Uri path.
     *
     * @return string
     */
    function images_url($path)
    {
        $urlPath = str_replace(PATH_STORAGE, '', $path);

        return base_url('images/' . $urlPath);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('prepare_url')) {
    /**
     * Prep URL
     *
     * Simply adds the http:// part if no scheme is included
     *
     * @param    string    the URL
     *
     * @return    string
     */
    function prepare_url($uri = '')
    {
        if ($uri === 'http://' or $uri === 'https://' or $uri === '') {
            return '';
        }

        /**
         * Converts double slashes in a string to a single slash,
         * except those found in http://
         *
         * http://www.some-site.com//index.php
         *
         * becomes:
         *
         * http://www.some-site.com/index.php
         */
        $uri = preg_replace('#(^|[^:])//+#', '\\1/', $uri);

        $url = parse_url($uri);

        if ( ! $url or ! isset($url[ 'scheme' ])) {
            return (is_https() ? 'https://' : 'http://') . $uri;
        }

        return $uri;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('redirect_url')) {
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Browser
     * Library's set_header() function.
     *
     * @param    string $uri    URL
     * @param    string $method Redirect method
     *                          'auto', 'location' or 'refresh'
     * @param    int    $code   HTTP Response status code
     *
     * @return    void
     */
    function redirect_url($uri = '', $method = 'auto', $code = null)
    {
        if (strpos($uri, 'http') === false) {
            $uri = base_url($uri);
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER[ 'SERVER_SOFTWARE' ]) && strpos(
                $_SERVER[ 'SERVER_SOFTWARE' ],
                'Microsoft-IIS'
            ) !== false
        ) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code))) {
            if (isset($_SERVER[ 'SERVER_PROTOCOL' ], $_SERVER[ 'REQUEST_METHOD' ]) && $_SERVER[ 'SERVER_PROTOCOL' ] === 'HTTP/1.1') {
                $code = ($_SERVER[ 'REQUEST_METHOD' ] !== 'GET')
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }

        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, true, $code);
                break;
        }

        exit;
    }
}