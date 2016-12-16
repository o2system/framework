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

if ( ! function_exists( 'str_capitalize' ) ) {
    /**
     * String Capitalize
     *
     * Capitalize a string
     *
     * @param    string $string     Source String
     * @param    bool   $first_word First Word Only
     *
     * @return    string
     */
    function str_capitalize ( $string, $first_word = false )
    {
        if ( ! empty( $string ) ) {
            if ( $first_word ) {
                return ucfirst( $string );
            } else {
                $string = explode( ' ', $string );

                $strings = array_map(
                    function ( $string ) {
                        return ucfirst( $string );
                    },
                    $string
                );

                return implode( ' ', $strings );
            }
        }

        return null;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'is_html' ) ) {
    /**
     * Determine if string is HTML
     *
     * @param $string
     *
     * @return bool
     */
    function is_html ( $string )
    {
        return $string != strip_tags( $string ) ? true : false;
    }
}

if ( ! function_exists( 'str_echo' ) ) {
    /**
     * str_echo
     *
     * @param mixed  $string
     * @param string $before
     * @param string $after
     *
     * @return string
     */
    function str_echo ( $string, $before = null, $after = null )
    {
        if ( ! empty( $string ) OR $string !== '' ) {
            return implode( ' ', func_get_args() );
        }

        return '';
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_email' ) ) {
    /**
     * Save display email
     *
     * @access    public
     *
     * @param    string
     * @param    integer    number of repeats
     *
     * @return    string
     */
    function str_email ( $string )
    {
        if ( ! empty( $string ) or $string != '' ) {
            return str_replace(
                [
                    '.',
                    '@',
                ],
                [
                    ' [dot] ',
                    ' [at] ',
                ],
                trim( $string )
            );
        }

        return $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_alphanumeric' ) ) {
    /**
     * Remove Non AlphaNumeric Characters
     *
     * @access    public
     *
     * @param    string
     * @param    integer    number of repeats
     *
     * @return    string
     */
    function str_alphanumeric ( $string )
    {
        if ( ! empty( $string ) or $string != '' ) {
            $string = preg_replace( "/[^a-zA-Z0-9\s]/", "", $string );
        }

        return trim( $string );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_numeric' ) ) {
    /**
     * Remove Non Numeric Characters
     *
     * @access    public
     *
     * @param    string
     * @param    integer    number of repeats
     *
     * @return    string
     */
    function str_numeric ( $string )
    {
        if ( ! empty( $string ) or $string != '' ) {
            $string = preg_replace( "/[^0-9\s]/", "", $string );
        }

        return trim( $string );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_truncate' ) ) {
    /**
     * Truncates a string to a certain length
     *
     * @param string $string
     * @param int    $limit
     * @param string $ending
     *
     * @return string
     */
    function str_truncate ( $string, $limit = 25, $ending = '' )
    {
        if ( strlen( $string ) > $limit ) {
            $string = strip_tags( $string );
            $string = substr( $string, 0, $limit );
            $string = substr( $string, 0, -( strlen( strrchr( $string, ' ' ) ) ) );
            $string = $string . $ending;
        }

        return $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_shorten' ) ) {
    /**
     * If a string is too long, shorten it in the middle
     *
     * @param string $string
     * @param int    $limit
     *
     * @return string
     */
    function str_shorten ( $string, $limit = 25 )
    {
        if ( strlen( $string ) > $limit ) {
            $pre = substr( $string, 0, ( $limit / 2 ) );
            $suf = substr( $string, -( $limit / 2 ) );
            $string = $pre . ' ... ' . $suf;
        }

        return $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_obfuscate' ) ) {
    /**
     * Scrambles the source of a string
     *
     * @param string $string
     *
     * @return string
     */
    function str_obfuscate ( $string )
    {
        $length = strlen( $string );
        $scrambled = '';
        for ( $i = 0; $i < $length; ++$i ) {
            $scrambled .= '&#' . ord( substr( $string, $i, 1 ) ) . ';';
        }

        return $scrambled;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'symbols_to_entities' ) ) {

    /**
     * Converts high-character symbols into their respective html entities.
     *
     * @return string
     *
     * @param  string $string
     */
    function symbols_to_entities ( $string )
    {
        static $symbols = [
            '‚', 'ƒ', '"', '…', '†', '‡', 'ˆ', '‰', 'Š', '‹', 'Œ', "'", "'", '"', '"', '•', '–', '—', '˜',
            '™', 'š', '›', 'œ', 'Ÿ', '€', 'Æ', 'Á', 'Â', 'À', 'Å', 'Ã', 'Ä', 'Ç',
            'Ð',
            'É',
            'Ê',
            'È',
            'Ë',
            'Í',
            'Î',
            'Ì',
            'Ï',
            'Ñ',
            'Ó',
            'Ô',
            'Ò',
            'Ø',
            'Õ',
            'Ö',
            'Þ',
            'Ú',
            'Û',
            'Ù',
            'Ü',
            'Ý',
            'á',
            'â',
            'æ',
            'à',
            'å',
            'ã',
            'ä',
            'ç',
            'é',
            'ê',
            'è',
            'ð',
            'ë',
            'í',
            'î',
            'ì',
            'ï',
            'ñ',
            'ó',
            'ô',
            'ò',
            'ø',
            'õ',
            'ö',
            'ß',
            'þ',
            'ú',
            'û',
            'ù',
            'ü',
            'ý',
            'ÿ',
            '¡',
            '£',
            '¤',
            '¥',
            '¦',
            '§',
            '¨',
            '©',
            'ª',
            '«',
            '¬',
            '­',
            '®',
            '¯',
            '°',
            '±',
            '²',
            '³',
            '´',
            'µ',
            '¶',
            '·',
            '¸',
            '¹',
            'º',
            '»',
            '¼',
            '½',
            '¾',
            '¿',
            '×',
            '÷',
            '¢',
            '…',
            'µ',
        ];
        static $entities = [
            '&#8218;',
            '&#402;',
            '&#8222;',
            '&#8230;',
            '&#8224;',
            '&#8225;',
            '&#710;',
            '&#8240;',
            '&#352;',
            '&#8249;',
            '&#338;',
            '&#8216;',
            '&#8217;',
            '&#8220;',
            '&#8221;',
            '&#8226;',
            '&#8211;',
            '&#8212;',
            '&#732;',
            '&#8482;',
            '&#353;',
            '&#8250;',
            '&#339;',
            '&#376;',
            '&#8364;',
            '&aelig;',
            '&aacute;',
            '&acirc;',
            '&agrave;',
            '&aring;',
            '&atilde;',
            '&auml;',
            '&ccedil;',
            '&eth;',
            '&eacute;',
            '&ecirc;',
            '&egrave;',
            '&euml;',
            '&iacute;',
            '&icirc;',
            '&igrave;',
            '&iuml;',
            '&ntilde;',
            '&oacute;',
            '&ocirc;',
            '&ograve;',
            '&oslash;',
            '&otilde;',
            '&ouml;',
            '&thorn;',
            '&uacute;',
            '&ucirc;',
            '&ugrave;',
            '&uuml;',
            '&yacute;',
            '&aacute;',
            '&acirc;',
            '&aelig;',
            '&agrave;',
            '&aring;',
            '&atilde;',
            '&auml;',
            '&ccedil;',
            '&eacute;',
            '&ecirc;',
            '&egrave;',
            '&eth;',
            '&euml;',
            '&iacute;',
            '&icirc;',
            '&igrave;',
            '&iuml;',
            '&ntilde;',
            '&oacute;',
            '&ocirc;',
            '&ograve;',
            '&oslash;',
            '&otilde;',
            '&ouml;',
            '&szlig;',
            '&thorn;',
            '&uacute;',
            '&ucirc;',
            '&ugrave;',
            '&uuml;',
            '&yacute;',
            '&yuml;',
            '&iexcl;',
            '&pound;',
            '&curren;',
            '&yen;',
            '&brvbar;',
            '&sect;',
            '&uml;',
            '&copy;',
            '&ordf;',
            '&laquo;',
            '&not;',
            '&shy;',
            '&reg;',
            '&macr;',
            '&deg;',
            '&plusmn;',
            '&sup2;',
            '&sup3;',
            '&acute;',
            '&micro;',
            '&para;',
            '&middot;',
            '&cedil;',
            '&sup1;',
            '&ordm;',
            '&raquo;',
            '&frac14;',
            '&frac12;',
            '&frac34;',
            '&iquest;',
            '&times;',
            '&divide;',
            '&cent;',
            '...',
            '&micro;',
        ];

        return str_replace( $symbols, $entities, $string );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'is_parse_string' ) ) {
    /**
     * Is Parse String
     *
     * @return string
     *
     * @params string $string
     */
    function is_parse_string ( $string )
    {
        if ( preg_match( '[=]', $string ) ) {
            return true;
        }

        return false;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'parse_string' ) ) {
    /**
     * Is Parse String
     *
     * @return string
     *
     * @params string $string
     */
    function parse_string ( $string )
    {
        if ( preg_match( '[=]', $string ) ) {
            parse_str( html_entity_decode( $string ), $string );
        }

        return $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'mb_stripos_all' ) ) {
    /**
     * mb_stripos all occurences
     * based on http://www.php.net/manual/en/function.strpos.php#87061
     *
     * Find all occurrences of a needle in a haystack
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return array or false
     */
    function mb_stripos_all ( $haystack, $needle )
    {

        $s = 0;
        $i = 0;

        while ( is_integer( $i ) ) {

            $i = mb_stripos( $haystack, $needle, $s );

            if ( is_integer( $i ) ) {
                $aStrPos[] = $i;
                $s = $i + mb_strlen( $needle );
            }
        }

        if ( isset( $aStrPos ) ) {
            return $aStrPos;
        } else {
            return false;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'is_serialized' ) ) {
    /**
     * Is Serialized
     *
     * Check is the string is serialized array
     *
     * @param   string $string Source string
     *
     * @return  bool
     */
    function is_serialized ( $string )
    {
        if ( ! is_string( $string ) ) {
            return false;
        }
        if ( trim( $string ) == '' ) {
            return false;
        }
        if ( preg_match( "/^(i|s|a|o|d)(.*);/si", $string ) ) {
            $is_valid = @unserialize( $string );

            if ( empty( $is_valid ) ) {
                return false;
            }

            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists( 'is_json' ) ) {
    /**
     * Is JSON
     *
     * Check is the string is json array or object
     *
     * @item    string or array
     * @return  boolean (true or false)
     */
    function is_json ( $string )
    {
        // make sure provided input is of type string
        if ( ! is_string( $string ) ) {
            return false;
        }
        // trim white spaces
        $string = trim( $string );
        // get first character
        $first_char = substr( $string, 0, 1 );
        // get last character
        $last_char = substr( $string, -1 );
        // check if there is a first and last character
        if ( ! $first_char || ! $last_char ) {
            return false;
        }
        // make sure first character is either { or [
        if ( $first_char !== '{' && $first_char !== '[' ) {
            return false;
        }
        // make sure last character is either } or ]
        if ( $last_char !== '}' && $last_char !== ']' ) {
            return false;
        }
        // let's leave the rest to PHP.
        // try to decode string
        json_decode( $string );
        // check if error occurred
        $is_valid = json_last_error() === JSON_ERROR_NONE;

        return $is_valid;
    }
}

if ( ! function_exists( 'strip_slashes' ) ) {
    /**
     * Strip Slashes
     *
     * Removes slashes contained in a string or in an array
     *
     * @param    mixed    string or array
     *
     * @return    mixed    string or array
     */
    function strip_slashes ( $str )
    {
        if ( ! is_array( $str ) ) {
            return stripslashes( $str );
        }

        foreach ( $str as $key => $val ) {
            $str[ $key ] = strip_slashes( $val );
        }

        return $str;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'strip_quotes' ) ) {
    /**
     * Strip Quotes
     *
     * Removes single and double quotes from a string
     *
     * @param    string
     *
     * @return    string
     */
    function strip_quotes ( $str )
    {
        return str_replace( [ '"', "'" ], '', $str );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'quotes_to_entities' ) ) {
    /**
     * Quotes to Entities
     *
     * Converts single and double quotes to entities
     *
     * @param    string
     *
     * @return    string
     */
    function quotes_to_entities ( $str )
    {
        return str_replace( [ "\'", "\"", "'", '"' ], [ "&#39;", "&quot;", "&#39;", "&quot;" ], $str );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_filter_slashes' ) ) {
    /**
     * str_remove_slashes
     *
     * Converts double slashes in a string to a single slash,
     * except those found in http://
     *
     * http://www.some-site.com//index.php
     *
     * becomes:
     *
     * http://www.some-site.com/index.php
     *
     * @param    string
     *
     * @return    string
     */
    function str_filter_slashes ( $str )
    {
        return preg_replace( '#(^|[^:])//+#', '\\1/', $str );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_filter_char' ) ) {
    /**
     * str_filter_char
     *
     * Reduces multiple instances of a particular character.  Example:
     *
     * Fred, Bill,, Joe, Jimmy
     *
     * becomes:
     *
     * Fred, Bill, Joe, Jimmy
     *
     * @param    string
     * @param    string    the character you wish to reduce
     * @param    bool      TRUE/FALSE - whether to trim the character from the beginning/end
     *
     * @return    string
     */
    function str_filter_char ( $str, $character = ',', $trim = false )
    {
        $str = preg_replace( '#' . preg_quote( $character, '#' ) . '{2,}#', $character, $str );

        return ( $trim === true ) ? trim( $str, $character ) : $str;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_rand' ) ) {
    /**
     * Create a Random String
     *
     * Useful for generating passwords or hashes.
     *
     * @param    string    type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
     * @param    int       number of characters
     *
     * @return    string
     */
    function str_rand ( $type = 'alnum', $len = 8 )
    {
        switch ( $type ) {
            case 'basic':
                return mt_rand();
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
                switch ( $type ) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }

                return substr( str_shuffle( str_repeat( $pool, ceil( $len / strlen( $pool ) ) ) ), 0, $len );
            case 'unique': // todo: remove in 3.1+
            case 'md5':
                return md5( uniqid( mt_rand() ) );
            case 'encrypt': // todo: remove in 3.1+
            case 'sha1':
                return sha1( uniqid( mt_rand(), true ) );
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_inc' ) ) {
    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param    string    required
     * @param    string    What should the duplicate number be appended with
     * @param    string    Which number should be used for the first dupe increment
     *
     * @return    string
     */
    function str_inc ( $str, $separator = '_', $first = 1 )
    {
        preg_match( '/(.+)' . $separator . '([0-9]+)$/', $str, $match );

        return isset( $match[ 2 ] ) ? $match[ 1 ] . $separator . ( $match[ 2 ] + 1 ) : $str . $separator . $first;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'str_alt' ) ) {
    /**
     * Alternator
     *
     * Allows strings to be alternated. See docs...
     *
     * @param    string (as many parameters as needed)
     *
     * @return    string
     */
    function str_alt ( $args )
    {
        static $i;

        if ( func_num_args() === 0 ) {
            $i = 0;

            return '';
        }
        $args = func_get_args();

        return $args[ ( $i++ % count( $args ) ) ];
    }
}

if ( ! function_exists( 'str_char_to_ascii' ) ) {
    /**
     * Convert Accented Foreign Characters to ASCII
     *
     * @param    string $string Input string
     *
     * @return    string
     */
    function str_chars_to_ascii ( $string )
    {
        static $array_from, $array_to;

        if ( ! is_array( $array_from ) ) {
            $foreign_characters = [
                '/ä|æ|ǽ/'                                                     => 'ae',
                '/ö|œ/'                                                       => 'oe',
                '/ü/'                                                         => 'ue',
                '/Ä/'                                                         => 'Ae',
                '/Ü/'                                                         => 'Ue',
                '/Ö/'                                                         => 'Oe',
                '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/'         => 'A',
                '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/'       => 'a',
                '/Б/'                                                         => 'B',
                '/б/'                                                         => 'b',
                '/Ç|Ć|Ĉ|Ċ|Č/'                                                 => 'C',
                '/ç|ć|ĉ|ċ|č/'                                                 => 'c',
                '/Д/'                                                         => 'D',
                '/д/'                                                         => 'd',
                '/Ð|Ď|Đ|Δ/'                                                   => 'Dj',
                '/ð|ď|đ|δ/'                                                   => 'dj',
                '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/'                 => 'E',
                '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/'                 => 'e',
                '/Ф/'                                                         => 'F',
                '/ф/'                                                         => 'f',
                '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/'                                             => 'G',
                '/ĝ|ğ|ġ|ģ|γ|г|ґ/'                                             => 'g',
                '/Ĥ|Ħ/'                                                       => 'H',
                '/ĥ|ħ/'                                                       => 'h',
                '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/'                     => 'I',
                '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/'                   => 'i',
                '/Ĵ/'                                                         => 'J',
                '/ĵ/'                                                         => 'j',
                '/Ķ|Κ|К/'                                                     => 'K',
                '/ķ|κ|к/'                                                     => 'k',
                '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/'                                             => 'L',
                '/ĺ|ļ|ľ|ŀ|ł|λ|л/'                                             => 'l',
                '/М/'                                                         => 'M',
                '/м/'                                                         => 'm',
                '/Ñ|Ń|Ņ|Ň|Ν|Н/'                                               => 'N',
                '/ñ|ń|ņ|ň|ŉ|ν|н/'                                             => 'n',
                '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/'   => 'O',
                '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
                '/П/'                                                         => 'P',
                '/п/'                                                         => 'p',
                '/Ŕ|Ŗ|Ř|Ρ|Р/'                                                 => 'R',
                '/ŕ|ŗ|ř|ρ|р/'                                                 => 'r',
                '/Ś|Ŝ|Ş|Ș|Š|Σ|С/'                                             => 'S',
                '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/'                                         => 's',
                '/Ț|Ţ|Ť|Ŧ|τ|Т/'                                               => 'T',
                '/ț|ţ|ť|ŧ|т/'                                                 => 't',
                '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/'           => 'U',
                '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/'       => 'u',
                '/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/'                                     => 'Y',
                '/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/'                                           => 'y',
                '/В/'                                                         => 'V',
                '/в/'                                                         => 'v',
                '/Ŵ/'                                                         => 'W',
                '/ŵ/'                                                         => 'w',
                '/Ź|Ż|Ž|Ζ|З/'                                                 => 'Z',
                '/ź|ż|ž|ζ|з/'                                                 => 'z',
                '/Æ|Ǽ/'                                                       => 'AE',
                '/ß/'                                                         => 'ss',
                '/Ĳ/'                                                         => 'IJ',
                '/ĳ/'                                                         => 'ij',
                '/Œ/'                                                         => 'OE',
                '/ƒ/'                                                         => 'f',
                '/ξ/'                                                         => 'ks',
                '/π/'                                                         => 'p',
                '/β/'                                                         => 'v',
                '/μ/'                                                         => 'm',
                '/ψ/'                                                         => 'ps',
                '/Ё/'                                                         => 'Yo',
                '/ё/'                                                         => 'yo',
                '/Є/'                                                         => 'Ye',
                '/є/'                                                         => 'ye',
                '/Ї/'                                                         => 'Yi',
                '/Ж/'                                                         => 'Zh',
                '/ж/'                                                         => 'zh',
                '/Х/'                                                         => 'Kh',
                '/х/'                                                         => 'kh',
                '/Ц/'                                                         => 'Ts',
                '/ц/'                                                         => 'ts',
                '/Ч/'                                                         => 'Ch',
                '/ч/'                                                         => 'ch',
                '/Ш/'                                                         => 'Sh',
                '/ш/'                                                         => 'sh',
                '/Щ/'                                                         => 'Shch',
                '/щ/'                                                         => 'shch',
                '/Ъ|ъ|Ь|ь/'                                                   => '',
                '/Ю/'                                                         => 'Yu',
                '/ю/'                                                         => 'yu',
                '/Я/'                                                         => 'Ya',
                '/я/'                                                         => 'ya',
            ];

            $array_from = array_keys( $foreign_characters );
            $array_to = array_values( $foreign_characters );
        }

        return preg_replace( $array_from, $array_to, $string );
    }
}

// ------------------------------------------------------------------------


if ( ! function_exists( 'str_entities_to_ascii' ) ) {
    /**
     * Entities to ASCII
     *
     * Converts character entities back to ASCII
     *
     * @param    string
     * @param    bool
     *
     * @return    string
     */
    function str_entities_to_ascii ( $string, $all = true )
    {
        if ( preg_match_all( '/\&#(\d+)\;/', $string, $matches ) ) {
            for ( $i = 0, $s = count( $matches[ 0 ] ); $i < $s; $i++ ) {
                $digits = $matches[ 1 ][ $i ];
                $out = '';

                if ( $digits < 128 ) {
                    $out .= chr( $digits );

                } elseif ( $digits < 2048 ) {
                    $out .= chr( 192 + ( ( $digits - ( $digits % 64 ) ) / 64 ) ) . chr( 128 + ( $digits % 64 ) );
                } else {
                    $out .= chr( 224 + ( ( $digits - ( $digits % 4096 ) ) / 4096 ) )
                            . chr( 128 + ( ( ( $digits % 4096 ) - ( $digits % 64 ) ) / 64 ) )
                            . chr( 128 + ( $digits % 64 ) );
                }

                $string = str_replace( $matches[ 0 ][ $i ], $out, $string );
            }
        }

        if ( $all ) {
            return str_replace(
                [ '&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;' ],
                [ '&', '<', '>', '"', "'", '-' ],
                $string
            );
        }

        return $string;
    }
}

// ------------------------------------------------------------------------


if ( ! function_exists( 'str_ascii_to_entities' ) ) {
    /**
     * High ASCII to Entities
     *
     * Converts high ASCII text and MS Word special characters to character entities
     *
     * @param    string $string
     *
     * @return    string
     */
    function str_ascii_to_entities ( $string )
    {
        $out = '';
        for ( $i = 0, $s = strlen( $string ) - 1, $count = 1, $temp = [ ]; $i <= $s; $i++ ) {
            $ordinal = ord( $string[ $i ] );

            if ( $ordinal < 128 ) {
                /*
                    If the $temp array has a value but we have moved on, then it seems only
                    fair that we output that entity and restart $temp before continuing. -Paul
                */
                if ( count( $temp ) === 1 ) {
                    $out .= '&#' . array_shift( $temp ) . ';';
                    $count = 1;
                }

                $out .= $string[ $i ];
            } else {
                if ( count( $temp ) === 0 ) {
                    $count = ( $ordinal < 224 ) ? 2 : 3;
                }

                $temp[] = $ordinal;

                if ( count( $temp ) === $count ) {
                    $number = ( $count === 3 )
                        ? ( ( $temp[ 0 ] % 16 ) * 4096 ) + ( ( $temp[ 1 ] % 64 ) * 64 ) + ( $temp[ 2 ] % 64 )
                        : ( ( $temp[ 0 ] % 32 ) * 64 ) + ( $temp[ 1 ] % 64 );

                    $out .= '&#' . $number . ';';
                    $count = 1;
                    $temp = [ ];
                } // If this is the last iteration, just output whatever we have
                elseif ( $i === $s ) {
                    $out .= '&#' . implode( ';', $temp ) . ';';
                }
            }
        }

        return $out;
    }
}

// ------------------------------------------------------------------------