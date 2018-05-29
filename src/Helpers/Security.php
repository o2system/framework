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
/**
 * Security Helper
 *
 * A collection of helper function for security purposes.
 */
// ------------------------------------------------------------------------

if ( ! function_exists('strip_image_tags')) {
    /**
     * strip_image_tags
     *
     * Strip all image tags from string of HTML source code.
     *
     * @param   string $source_code The string of HTML source code.
     *
     * @return  string
     */
    function strip_image_tags($source_code)
    {
        return preg_replace(
            [
                '#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
                '#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i',
            ],
            '\\2',
            $source_code
        );
    }
}

//--------------------------------------------------------------------

if ( ! function_exists('strip_cdata')) {
    /**
     * strip_cdata
     *
     * Strip all CDATA encapsulation from string of HTML source code.
     *
     * @param   string $source_code The string of HTML source code.
     *
     * @return  string
     */
    function strip_cdata($source_code)
    {
        preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $source_code, $matches);

        return str_replace($matches[ 0 ], $matches[ 1 ], $source_code);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('strips_all_tags')) {
    /**
     * strips_all_tags
     *
     * Strip all HTML tags from string of HTML source code but keep safe
     * the original content.
     *
     * @param   string $source_code The string of HTML source code.
     *
     * @return  string
     */
    function strips_all_tags($source_code)
    {
        return preg_replace([
            '@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'  // Strip multi-line comments including CDATA
        ], '', $source_code);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('strips_tags')) {
    /**
     * strips_tags
     *
     * Strips all HTML tags and its content at the defined tags.
     * Strip out all the content between any tag that has an opening and closing tag, like <table>, <object>, etc.
     *
     * @param   string  $source_code     The string of HTML source code.
     * @param   string  $disallowed_tags The list of disallowed HTML tags, separated with |.
     * @param    string $allowed_tags    The list of allowed HTML tags, separated with |.
     *
     * @return  string
     */
    function strips_tags($source_code, $disallowed_tags = 'script|style|noframes|select|option', $allowed_tags = '')
    {
        //prep the string
        $source_code = ' ' . $source_code;

        //initialize keep tag logic
        if (strlen($allowed_tags) > 0) {
            $k = explode('|', $allowed_tags);
            for ($i = 0; $i < count($k); $i++) {
                $source_code = str_replace('<' . $k[ $i ], '[{(' . $k[ $i ], $source_code);
                $source_code = str_replace('</' . $k[ $i ], '[{(/' . $k[ $i ], $source_code);
            }
        }
        //begin removal
        //remove comment blocks
        while (stripos($source_code, '<!--') > 0) {
            $pos[ 1 ] = stripos($source_code, '<!--');
            $pos[ 2 ] = stripos($source_code, '-->', $pos[ 1 ]);
            $len[ 1 ] = $pos[ 2 ] - $pos[ 1 ] + 3;
            $x = substr($source_code, $pos[ 1 ], $len[ 1 ]);
            $source_code = str_replace($x, '', $source_code);
        }
        //remove tags with content between them
        if (strlen($disallowed_tags) > 0) {
            $e = explode('|', $disallowed_tags);
            for ($i = 0; $i < count($e); $i++) {
                while (stripos($source_code, '<' . $e[ $i ]) > 0) {
                    $len[ 1 ] = strlen('<' . $e[ $i ]);
                    $pos[ 1 ] = stripos($source_code, '<' . $e[ $i ]);
                    $pos[ 2 ] = stripos($source_code, $e[ $i ] . '>', $pos[ 1 ] + $len[ 1 ]);
                    $len[ 2 ] = $pos[ 2 ] - $pos[ 1 ] + $len[ 1 ];
                    $x = substr($source_code, $pos[ 1 ], $len[ 2 ]);
                    $source_code = str_replace($x, '', $source_code);
                }
            }
        }
        //remove remaining tags
        while (stripos($source_code, '<') > 0) {
            $pos[ 1 ] = stripos($source_code, '<');
            $pos[ 2 ] = stripos($source_code, '>', $pos[ 1 ]);
            $len[ 1 ] = $pos[ 2 ] - $pos[ 1 ] + 1;
            $x = substr($source_code, $pos[ 1 ], $len[ 1 ]);
            $source_code = str_replace($x, '', $source_code);
        }
        //finalize keep tag
        if (strlen($allowed_tags) > 0) {
            for ($i = 0; $i < count($k); $i++) {
                $source_code = str_replace('[{(' . $k[ $i ], '<' . $k[ $i ], $source_code);
                $source_code = str_replace('[{(/' . $k[ $i ], '</' . $k[ $i ], $source_code);
            }
        }

        return trim($source_code);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_word_doc')) {
    /**
     * strip_word_doc
     *
     * Strip all word doc tags from string of source code.
     *
     * @param   string $source_code  The string of HTML source code.
     * @param   string $allowed_tags The list of disallowed HTML tags, separated with |.
     *
     * @return  string
     */
    function strip_word_doc($source_code, $allowed_tags = '')
    {
        mb_regex_encoding('UTF-8');

        //replace MS special characters first
        $search = [
            '/&lsquo;/u',
            '/&rsquo;/u',
            '/&ldquo;/u',
            '/&rdquo;/u',
            '/&mdash;/u',
        ];
        $replace = [
            '\'',
            '\'',
            '"',
            '"',
            '-',
        ];
        $source_code = preg_replace($search, $replace, $source_code);

        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $source_code = html_entity_decode($source_code, ENT_QUOTES, 'UTF-8');

        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if (mb_stripos($source_code, '/*') !== false) {
            $source_code = mb_eregi_replace('#/\*.*?\*/#s', '', $source_code, 'm');
        }

        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $source_code = preg_replace(
            [
                '/<([0-9]+)/',
            ],
            [
                '< $1',
            ],
            $source_code
        );
        $source_code = strip_tags($source_code, $allowed_tags);

        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $source_code = preg_replace(
            [
                '/^\s\s+/',
                '/\s\s+$/',
                '/\s\s+/u',
            ],
            [
                '',
                '',
                ' ',
            ],
            $source_code
        );

        //strip out inline css and simplify style tags
        $search = [
            '#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu',
            '#<(em|i)[^>]*>(.*?)</(em|i)>#isu',
            '#<u[^>]*>(.*?)</u>#isu',
        ];
        $replace = [
            '<b>$2</b>',
            '<i>$2</i>',
            '<u>$1</u>',
        ];
        $source_code = preg_replace($search, $replace, $source_code);

        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $source_code, $matches);
        if ($num_matches) {
            $source_code = preg_replace('/\<!--(.)*--\>/isu', '', $source_code);
        }

        return $source_code;
    }
}

//--------------------------------------------------------------------

if ( ! function_exists('strip_slashes_recursive')) {
    /**
     * strip_slashes_recursive
     *
     * Recursive Strip Slashes
     *
     * Un-quotes a quoted string
     *
     * @link  http://php.net/manual/en/function.stripslashes.php
     *
     * @param string $string <p>
     *                       The input string.
     *                       </p>
     *
     * @return string a string with backslashes stripped off.
     * (\' becomes ' and so on.)
     * Double backslashes (\\) are made into a single
     * backslash (\).
     * @since 4.0
     * @since 5.0
     */
    function strip_slashes_recursive($string)
    {
        $string = is_array($string) ? array_map('strip_slashes_recursive', $string) : stripslashes($string);

        return $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_comments')) {
    /**
     * strip_comments
     *
     * Strip HTML Comments
     *
     * @param   string $source_code HTML Source Code
     *
     * @return  string
     */
    function strip_comments($source_code)
    {
        return preg_replace('/<!--[\s\S]*?-->/', '', $source_code);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('clean_white_space')) {
    /**
     * clean_white_space
     *
     * Clean HTML Whitespace
     *
     * @param   string $source_code HTML Source Code
     *
     * @return  string
     */
    function clean_white_space($source_code)
    {
        $source_code = str_replace(["\n", "\r", '&nbsp;', "\t"], '', $source_code);

        return preg_replace('|  +|', ' ', $source_code);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('encode_php_tags')) {
    /**
     * encode_php_tags
     *
     * Encode PHP tags to entities.
     *
     * @param   string $string The string tobe encoded.
     *
     * @return  string
     */
    function encode_php_tags($string)
    {
        return str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $string);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('escape_html')) {
    /**
     * escape_html
     *
     * Returns HTML escaped variable.
     *
     * @param    mixed $source_code   The input string or array of strings to be escaped.
     * @param    bool  $double_encode $double_encode set to FALSE prevents escaping twice.
     *
     * @return    mixed            The escaped string or array of strings as a result.
     */
    function escape_html($source_code, $encoding = 'UTF-8', $double_encode = true)
    {
        if (is_array($source_code)) {
            return array_map('escape_html', $source_code, array_fill(0, count($source_code), $double_encode));
        }

        return htmlspecialchars($source_code, ENT_QUOTES, $encoding, $double_encode);
    }
}