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

if ( ! function_exists('base_url')) {
    /**
     * base_url
     *
     * @param null $segments
     * @param null $query
     *
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function base_url($segments = null, $query = null): string
    {
        $uri = (new \O2System\Kernel\Http\Message\Uri())
            ->withSegments(new \O2System\Kernel\Http\Message\Uri\Segments(''))
            ->withQuery('');

        if($uri->getSubDomain() !== reset(globals()->app->getSegments())) {
            if(!empty($appSegments = globals()->app->getSegments())) {
                if(is_string($segments)) {
                    $segments = explode('/', $segments);
                    $segments = array_diff($segments, $appSegments);
                } elseif(is_null($segments)) {
                    $segments = [];
                }

                $segments = array_merge($appSegments, $segments);
            }
        }

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

if ( ! function_exists('domain_url')) {
    /**
     * domain_url
     *
     * @param null $segments
     * @param null $query
     * @param null $subdomain
     *
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function domain_url($segments = null, $query = null, $subdomain = null): string
    {
        $uri = (new \O2System\Kernel\Http\Message\Uri())
            ->withSegments(new \O2System\Kernel\Http\Message\Uri\Segments(''))
            ->withQuery('');

        if(is_null($subdomain)) {
            $uri->domain->setSubDomain('');
        } elseif(isset($subdomain)) {
            $uri->domain->setSubDomain($subdomain);
        }

        if ($uriConfig = config()->offsetGet('uri')) {
            if ( ! empty($uriConfig[ 'base' ])) {
                $base = (is_https() ? 'https' : 'http') . '://' . str_replace(['http://', 'https://'], '',
                        $uriConfig[ 'base' ]);
                $uri = (new \O2System\Kernel\Http\Message\Uri($base))
                    ->withSegments(new \O2System\Kernel\Http\Message\Uri\Segments(''))
                    ->withQuery('');
            }
        }

        if (isset($query)) {
            $uri = $uri->addQuery($query);
        }

        if (isset($segments)) {
            $uri = $uri->addSegments($segments);
        }

        return $uri->__toString();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('current_url')) {
    /**
     * current_url
     *
     * @param null $segments
     * @param null $query
     *
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function current_url($segments = null, $query = null): string
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

if ( ! function_exists('public_url')) {
    /**
     * public_url
     *
     * @param string $path Uri path.
     *
     * @return string
     */
    function public_url(string $path): string
    {
        return path_to_url($path);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('assets_url')) {
    /**
     * assets_url
     *
     * @param string $path Uri path.
     *
     * @return string
     */
    function assets_url(string $path): string
    {
        return presenter()->assets->file($path);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('storage_url')) {
    /**
     * storage_url
     *
     * @param string $path Uri path.
     *
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function storage_url(string $path): string
    {
        $urlPath = str_replace(PATH_STORAGE, '', $path);

        return base_url('storage/' . $urlPath);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('resources_url')) {
    /**
     * resource_url
     *
     * @param string $path Uri path.
     *
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function resources_url(string $path): string
    {
        $urlPath = str_replace(PATH_RESOURCES, '', $path);

        return base_url('resources/' . $urlPath);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('images_url')) {
    /**
     * images_url
     *
     * @param string $path Uri path.
     * @param null $size
     * @return string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function images_url(string $path, $size = null): string
    {
        $urlPath = str_replace(PATH_STORAGE, '', $path);

        return base_url('images/' . (isset($size) ? '/' . $size : '') . $urlPath);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('prepare_url')) {
    /**
     * prepare_url
     *
     * Simply adds the http:// part if no scheme is included
     *
     * @param    string    the URL
     *
     * @return    string
     */
    function prepare_url($uri = ''): string
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
     * redirect_url
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Browser
     * Library's set_header() function.
     *
     * @param string $uri URL
     * @param string $method Redirect method
     *                          'auto', 'location' or 'refresh'
     * @param null $code HTTP Response status code
     *
     * @return    void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    function redirect_url(string $uri = '', string $method = 'auto', $code = null)
    {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

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
