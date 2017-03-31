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

if ( ! function_exists( 'html' ) ) {
    /**
     * HTML Helper
     *
     * @example
     * html('div', array('id' => 'div_id','class' => 'div_class'), 'this is div content');
     * output: <div id="div_id" class="div_class">this is div content</div>
     *
     * html('div',array('id' => 'div_id','class' => 'div_class'));
     * output: <div id="div_id" class="div_class">
     *
     * html('/div');
     * output: </div>
     *
     * @param string $tag     HTML Tag
     * @param array  $attr    HTML Tag Attributes
     * @param string $content HTML Tag Content
     *
     * @return string
     */
    function html( $tag = '', $attr = [], $content = '' )
    {
        if ( strpos( $tag, '/' ) === false ) {
            $open_tag = "<" . $tag;

            if ( is_array( $attr ) ) {
                $open_tag .= stringify_attributes( $attr );
            } elseif ( is_string( $attr ) ) {
                $content = $attr;
            }

            if ( $content != '' ) {
                if ( $content == '/' ) {
                    return $open_tag . "/>";
                } elseif ( $content == '/' . $tag ) {
                    return $open_tag . "></" . $tag . ">";
                } else {
                    return $open_tag . '>' . $content . "</" . $tag . ">";
                }
            } else {
                return $open_tag . '>';
            }
        }

        return "<" . $tag . ">";
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strip_comments' ) ) {
    /**
     * Strip HTML Comments
     *
     * @param   string $html HTML Source Code
     *
     * @return  string
     */
    function strip_comments( $html )
    {
        $expr = '/<!--[\s\S]*?-->/';
        $func = 'rhc';
        $html = preg_replace( $expr, '', $html );

        return $html;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strip_js' ) ) {
    /**
     * Strip HTML Javascript
     *
     * @param   string $html HTML Source Code
     *
     * @return  string
     */
    function strip_js( $html )
    {
        $html = htmlspecialchars_decode( $html );
        $search_arr = [ '<script', '</script>' ];
        $html = str_ireplace( $search_arr, $search_arr, $html );
        $split_arr = explode( '<script', $html );
        $remove_jscode_arr = [];
        foreach ( $split_arr as $key => $val ) {
            $newarr = explode( '</script>', $split_arr[ $key ] );
            $remove_jscode_arr[] = ( $key == 0 ) ? $newarr[ 0 ] : $newarr[ 1 ];
        }

        return implode( '', $remove_jscode_arr );
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'video' ) ) {
    /**
     * Video Generator
     *
     * @param array  $attr
     * @param array  $sources
     * @param string $no_support_message
     *
     * @return string
     */
    function video(
        $attr = [],
        $sources = [],
        $no_support_message = 'Your browser does not support the HTML5 video tag'
    ) {

        return html( 'video', $attr, $no_support_message );
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'canvas' ) ) {
    /**
     * Canvas
     *
     * Generates <canvas> element
     *
     * @param   array  $attr               Canvas Tag Attributes
     * @param   string $no_support_message Unsupported Browser Warning Message
     *
     * @return  string
     */
    function canvas( $attr = [], $no_support_message = 'Your browser does not support the HTML5 canvas tag' )
    {
        return html( 'canvas', $attr, $no_support_message );
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'audio' ) ) {
    /**
     * Audio
     *
     * Generates <audio> element
     *
     * @param   array  $attr               Video Tag Attributes
     * @param   array  $sources            Video Sources
     * @param   string $no_support_message Unsupported Browser Warning Message
     *
     * @return  string
     */
    function audio(
        $attr = [],
        $sources = [],
        $no_support_message = 'Your browser does not support the HTML5 audio tag'
    ) {
        return html( 'audio', $attr, $no_support_message );
    }
}

// ------------------------------------------------------------------------


if ( ! function_exists( 'parse_attributes' ) ) {
    /**
     * Parse attributes
     *
     * Parse attributes for HTML elements
     *
     * @param   string $attr HTML Attributes
     *
     * @access  private
     * @return  string
     */
    function parse_attributes( $attr )
    {
        if ( is_string( $attr ) ) {
            if ( is_html( $attr ) ) {
                $xml = simplexml_load_string( str_replace( '>', '/>', $attr ) );
            } else {
                $xml = simplexml_load_string( '<tag ' . $attr . '/>' );
            }

            $attr = [];

            foreach ( $xml->attributes() as $key => $node ) {
                $attr[ $key ] = (string)$node;
            }

            return $attr;
        }

        return [];
    }
}

// ------------------------------------------------------------------------


if ( ! function_exists( 'parse_sources' ) ) {
    /**
     * Parse Sources
     *
     * Generates sources for the <audio> and <video> elements
     *
     * @param   array $sources Sources Audio / Video Elements
     *
     * @access  private
     * @return  string
     */
    function parse_sources( $sources = [] )
    {
        if ( empty( $sources ) ) {
            return null;
        }

        $html = null;

        foreach ( $sources as $source ) {
            $html .= '<source src="' . $source[ 'src' ] . '"';

            if ( isset( $source[ 'type' ] ) ) {
                $html .= ' type="' . $source[ 'type' ] . '"';
            }

            if ( isset( $source[ 'media' ] ) ) {
                $html .= ' media="' . $source[ 'media' ] . '"';
            }

            if ( isset( $source[ 'attr' ] ) && ! empty( $source[ 'attr' ] ) ) {
                $html .= ' ' . stringify_attributes( $source[ 'attr' ] );
            }

            $html .= ' />';
        }

        return $html;
    }
}

if ( ! function_exists( 'strip_image_tags' ) ) {
    /**
     * Strip Image Tags
     *
     * @param   string $html HTML Source Code
     *
     * @return  string
     */
    function strip_image_tags( $html )
    {
        $html = preg_replace( "#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "\\1", $html );
        $html = preg_replace( "#<img\s+.*?src\s*=\s*(.+?).*?\>#", "\\1", $html );

        return $html;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strip_cdata' ) ) {
    /**
     * Strip CData
     *
     * An easy way to clean a string of all CDATA encapsulation.
     *
     * @param string $html HTML Source Code
     *
     * @return  string
     */
    function strip_cdata( $html )
    {
        preg_match_all( '/<!\[cdata\[(.*?)\]\]>/is', $html, $matches );

        return str_replace( $matches[ 0 ], $matches[ 1 ], $html );
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strips_all_tags' ) ) {
    /**
     * Clean all HTML tags but keep safe the original content.
     *
     * @param   string $html HTML Source Code
     *
     * @return  string
     */
    function strips_all_tags( $html )
    {
        $search = [
            '@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'  // Strip multi-line comments including CDATA
        ];
        $result = preg_replace( $search, '', $html );

        return $result;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strip_word_doc' ) ) {
    /**
     * Strip all tags from word document or html.
     *
     * @param   string $html         HTML Source Code
     * @param   string $allowed_tags = '<b><i><sup><sub><em><strong><u><br>'
     *
     * @return  string
     */
    function strip_word_doc( $html, $allowed_tags = '' )
    {
        mb_regex_encoding( 'UTF-8' );

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
        $html = preg_replace( $search, $replace, $html );

        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $html = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );

        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if ( mb_stripos( $html, '/*' ) !== false ) {
            $html = mb_eregi_replace( '#/\*.*?\*/#s', '', $html, 'm' );
        }

        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $html = preg_replace(
            [
                '/<([0-9]+)/',
            ],
            [
                '< $1',
            ],
            $html
        );
        $html = strip_tags( $html, $allowed_tags );

        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $html = preg_replace(
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
            $html
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
        $html = preg_replace( $search, $replace, $html );

        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all( "/\<!--/u", $html, $matches );
        if ( $num_matches ) {
            $html = preg_replace( '/\<!--(.)*--\>/isu', '', $html );
        }

        return $html;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'remove_tags' ) ) {
    /**
     * Remove Tag
     *
     * Remove the tags but keep the content.
     * Note this function always assumed no two tags start the same way (e.g. <tag> and <tags>)
     *
     * @param   string       $html          HTML Source Code
     * @param   string|array $tags          Single HTML Tag | List of HTML Tag
     * @param   bool         $strip_content Whether to display the content of inside tag or erase it
     *
     * @return  string
     */
    function remove_tags( $html, $tags, $strip_content = false )
    {
        $content = '';
        if ( ! is_array( $tags ) ) {
            $tags = ( strpos( $html, '>' ) !== false ? explode( '>', str_replace( '<', '', $tags ) ) : [ $tags ] );
            if ( end( $tags ) == '' ) {
                array_pop( $tags );
            }
        }
        foreach ( $tags as $tag ) {
            if ( $strip_content ) {
                $content = '(.+</' . $tag . '[^>]*>|)';
            }

            $html = preg_replace( '#</?' . $tag . '[^>]*>' . $content . '#is', '', $html );
        }

        return $html;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'extract_tag' ) ) {
    /**
     * Extract Tag
     *
     * Extract content inside tag.
     *
     * @param  string  $html HTML Source Code
     * @param   string $tag  HTML Tag
     *
     * @return  string
     */
    function extract_tag( $html, $tag = 'div' )
    {
        $html = preg_match_all( "/(\<" . $tag . ")(.*?)(" . $tag . ">)/si", $html, $matches );

        $result = [];
        foreach ( $matches[ 0 ] as $item ) {
            $result[] = preg_replace( "/\<[\/]?" . $tag . "\>/", '', $item );
        }

        return $result;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'strips_tags' ) ) {
    /**
     * Strip Tags
     *
     * Strips all HTML tags and its content at the defined tags.
     * Strip out all the content between any tag that has an opening and closing tag, like <table>, <object>, etc.
     *
     * @param  string $html           HTML Source Code
     * @param  string $disallowed_tag Disallowed HTML Tag separated with |
     * @param  string $allowed_tag    Allowed HTML Tag separated with |
     *
     * @return  string
     */
    function strips_tags( $html, $disallowed_tag = 'script|style|noframes|select|option', $allowed_tag = '' )
    {
        //prep the string
        $html = ' ' . $html;

        //initialize keep tag logic
        if ( strlen( $allowed_tag ) > 0 ) {
            $k = explode( '|', $allowed_tag );
            for ( $i = 0; $i < count( $k ); $i++ ) {
                $html = str_replace( '<' . $k[ $i ], '[{(' . $k[ $i ], $html );
                $html = str_replace( '</' . $k[ $i ], '[{(/' . $k[ $i ], $html );
            }
        }
        //begin removal
        //remove comment blocks
        while ( stripos( $html, '<!--' ) > 0 ) {
            $pos[ 1 ] = stripos( $html, '<!--' );
            $pos[ 2 ] = stripos( $html, '-->', $pos[ 1 ] );
            $len[ 1 ] = $pos[ 2 ] - $pos[ 1 ] + 3;
            $x = substr( $html, $pos[ 1 ], $len[ 1 ] );
            $html = str_replace( $x, '', $html );
        }
        //remove tags with content between them
        if ( strlen( $disallowed_tag ) > 0 ) {
            $e = explode( '|', $disallowed_tag );
            for ( $i = 0; $i < count( $e ); $i++ ) {
                while ( stripos( $html, '<' . $e[ $i ] ) > 0 ) {
                    $len[ 1 ] = strlen( '<' . $e[ $i ] );
                    $pos[ 1 ] = stripos( $html, '<' . $e[ $i ] );
                    $pos[ 2 ] = stripos( $html, $e[ $i ] . '>', $pos[ 1 ] + $len[ 1 ] );
                    $len[ 2 ] = $pos[ 2 ] - $pos[ 1 ] + $len[ 1 ];
                    $x = substr( $html, $pos[ 1 ], $len[ 2 ] );
                    $html = str_replace( $x, '', $html );
                }
            }
        }
        //remove remaining tags
        while ( stripos( $html, '<' ) > 0 ) {
            $pos[ 1 ] = stripos( $html, '<' );
            $pos[ 2 ] = stripos( $html, '>', $pos[ 1 ] );
            $len[ 1 ] = $pos[ 2 ] - $pos[ 1 ] + 1;
            $x = substr( $html, $pos[ 1 ], $len[ 1 ] );
            $html = str_replace( $x, '', $html );
        }
        //finalize keep tag
        if ( strlen( $allowed_tag ) > 0 ) {
            for ( $i = 0; $i < count( $k ); $i++ ) {
                $html = str_replace( '[{(' . $k[ $i ], '<' . $k[ $i ], $html );
                $html = str_replace( '[{(/' . $k[ $i ], '</' . $k[ $i ], $html );
            }
        }

        return trim( $html );
    }
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'clean_white_space' ) ) {
    /**
     * Clean HTML Whitespace
     *
     * @param   string $html HTML Source Code
     *
     * @return  string
     */
    function clean_white_space( $html = '' )
    {
        $html = str_replace( [ "\n", "\r", '&nbsp;', "\t" ], '', $html );

        return preg_replace( '|  +|', ' ', $html );
    }
}

if ( ! function_exists( 'heading' ) ) {
    /**
     * Heading
     *
     * Generates an HTML heading tag.
     *
     * @param    string    content
     * @param    int       heading level
     * @param    string
     *
     * @return    string
     */
    function heading( $data = '', $h = '1', $attributes = '' )
    {
        return '<h' . $h . stringify_attributes( $attributes ) . '>' . $data . '</h' . $h . '>';
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'ul' ) ) {
    /**
     * Unordered List
     *
     * Generates an HTML unordered list from an single or multi-dimensional array.
     *
     * @param    array
     * @param    mixed
     *
     * @return    string
     */
    function ul( $list, $attributes = '' )
    {
        return _list( 'ul', $list, $attributes );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'ol' ) ) {
    /**
     * Ordered List
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param    array
     * @param    mixed
     *
     * @return    string
     */
    function ol( $list, $attributes = '' )
    {
        return _list( 'ol', $list, $attributes );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( '_list' ) ) {
    /**
     * Generates the list
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param    string
     * @param    mixed
     * @param    mixed
     * @param    int
     *
     * @return    string
     */
    function _list( $type = 'ul', $list = [], $attributes = '', $depth = 0 )
    {
        // If an array wasn't submitted there's nothing to do...
        if ( ! is_array( $list ) ) {
            return $list;
        }

        // Set the indentation based on the depth
        $out = str_repeat( ' ', $depth )
            // Write the opening list tag
            . '<' . $type . stringify_attributes( $attributes ) . ">\n";


        // Cycle through the list elements.  If an array is
        // encountered we will recursively call list()

        static $_last_list_item = '';
        foreach ( $list as $key => $val ) {
            $_last_list_item = $key;

            $out .= str_repeat( ' ', $depth + 2 ) . '<li>';

            if ( ! is_array( $val ) ) {
                $out .= $val;
            } else {
                $out .= $_last_list_item . "\n" . _list( $type, $val, '', $depth + 4 ) . str_repeat( ' ', $depth + 2 );
            }

            $out .= "</li>\n";
        }

        // Set the indentation for the closing tag and apply it
        return $out . str_repeat( ' ', $depth ) . '</' . $type . ">\n";
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'img' ) ) {
    /**
     * Image
     *
     * Generates an <img /> element
     *
     * @param    mixed
     * @param    bool
     * @param    mixed
     *
     * @return    string
     */
    function img( $src = '', $index_page = false, $attributes = '' )
    {
        if ( ! is_array( $src ) ) {
            $src = [ 'src' => $src ];
        }

        // If there is no alt attribute defined, set it to an empty string
        if ( ! isset( $src[ 'alt' ] ) ) {
            $src[ 'alt' ] = '';
        }

        $img = '<img';

        foreach ( $src as $k => $v ) {
            if ( $k === 'src' && ! preg_match( '#^([a-z]+:)?//#i', $v ) ) {
                if ( $index_page === true ) {
                    $img .= ' src="' . get_instance()->config->site_url( $v ) . '"';
                } else {
                    $img .= ' src="' . o2system()->config->slash_item( 'base_url' ) . $v . '"';
                }
            } else {
                $img .= ' ' . $k . '="' . $v . '"';
            }
        }

        return $img . stringify_attributes( $attributes ) . ' />';
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'link_tag' ) ) {
    /**
     * Link
     *
     * Generates link to a CSS file
     *
     * @param    mixed     stylesheet hrefs or an array
     * @param    string    rel
     * @param    string    type
     * @param    string    title
     * @param    string    media
     * @param    bool      should index_page be added to the css path
     *
     * @return    string
     */
    function link_tag(
        $href = '',
        $rel = 'stylesheet',
        $type = 'text/css',
        $title = '',
        $media = '',
        $index_page = false
    ) {
        $link = '<link ';

        if ( is_array( $href ) ) {
            foreach ( $href as $k => $v ) {
                if ( $k === 'href' && ! preg_match( '#^([a-z]+:)?//#i', $v ) ) {
                    if ( $index_page === true ) {
                        $link .= 'href="' . path_to_url( PATH_ROOT . '/index.php/' . $v ) . '" ';
                    } else {
                        $link .= 'href="' . path_to_url( PATH_ROOT . '/' . $v ) . '" ';
                    }
                } else {
                    $link .= $k . '="' . $v . '" ';
                }
            }
        } else {
            if ( preg_match( '#^([a-z]+:)?//#i', $href ) ) {
                $link .= 'href="' . $href . '" ';
            } elseif ( $index_page === true ) {
                $link .= 'href="' . path_to_url( $href ) . '" ';
            } else {
                $link .= 'href="' . path_to_url( PATH_ROOT . '/' . $href ) . '" ';
            }

            $link .= 'rel="' . $rel . '" type="' . $type . '" ';

            if ( $media !== '' ) {
                $link .= 'media="' . $media . '" ';
            }

            if ( $title !== '' ) {
                $link .= 'title="' . $title . '" ';
            }
        }

        return $link . "/>\n";
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'meta' ) ) {
    /**
     * Generates meta tags from an array of key/values
     *
     * @param    array
     * @param    string
     * @param    string
     * @param    string
     *
     * @return    string
     */
    function meta( $name = '', $content = '', $type = 'name', $newline = "\n" )
    {
        // Since we allow the data to be passes as a string, a simple array
        // or a multidimensional one, we need to do a little prepping.
        if ( ! is_array( $name ) ) {
            $name = [ [ 'name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline ] ];
        } elseif ( isset( $name[ 'name' ] ) ) {
            // Turn single array into multidimensional
            $name = [ $name ];
        }

        $str = '';
        foreach ( $name as $meta ) {
            $type = ( isset( $meta[ 'type' ] ) && $meta[ 'type' ] !== 'name' ) ? 'http-equiv' : 'name';
            $name = isset( $meta[ 'name' ] ) ? $meta[ 'name' ] : '';
            $content = isset( $meta[ 'content' ] ) ? $meta[ 'content' ] : '';
            $newline = isset( $meta[ 'newline' ] ) ? $meta[ 'newline' ] : "\n";

            $str .= '<meta ' . $type . '="' . $name . '" content="' . $content . '" />' . $newline;
        }

        return $str;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'br' ) ) {
    /**
     * Generates HTML BR tags based on number supplied
     *
     * @deprecated    3.0.0    Use str_repeat() instead
     *
     * @param    int $count Number of times to repeat the tag
     *
     * @return    string
     */
    function br( $count = 1 )
    {
        return str_repeat( '<br />', $count );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'nbs' ) ) {
    /**
     * Generates non-breaking space entities based on number supplied
     *
     * @deprecated    3.0.0    Use str_repeat() instead
     *
     * @param    int
     *
     * @return    string
     */
    function nbs( $num = 1 )
    {
        return str_repeat( '&nbsp;', $num );
    }
}

if ( ! function_exists( 'highlight_code' ) ) {
    /**
     * Code Highlighter
     *
     * Colorizes code strings
     *
     * @param    string    the text string
     *
     * @return    string
     */
    function highlight_code( $str )
    {
        /* The highlight string function encodes and highlights
         * brackets so we need them to start raw.
         *
         * Also replace any existing PHP tags to temporary markers
         * so they don't accidentally break the string out of PHP,
         * and thus, thwart the highlighting.
         */
        $str = str_replace(
            [ '&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>' ],
            [ '<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose' ],
            $str
        );

        // The highlight_string function requires that the text be surrounded
        // by PHP tags, which we will remove later
        $str = highlight_string( '<?php ' . $str . ' ?>', true );

        // Remove our artificially added PHP, and the syntax highlighting that came with it
        $str = preg_replace(
            [
                '/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
                '/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
                '/<span style="color: #[A-Z0-9]+"\><\/span>/i',
            ],
            [
                '<span style="color: #$1">',
                "$1</span>\n</span>\n</code>",
                '',
            ],
            $str
        );

        // Replace our markers back to PHP tags.
        return str_replace(
            [ 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose' ],
            [ '&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;' ],
            $str
        );
    }
}

// ------------------------------------------------------------------------
