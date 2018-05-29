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

if ( ! function_exists('text_split')) {
    /**
     * text_split
     *
     * Split text into array without losing any words.
     *
     * @params string $text
     *
     * @param   string $text     Text Source
     * @param   string $splitter Split Text Marker
     * @param   int    $limit    Split after num of limit characters
     *
     * @return string
     */
    function text_split($text, $splitter = '<---text-split--->', $limit = 100)
    {
        $wrap_text = wordwrap($text, $limit, $splitter);
        $wrap_text = preg_split('[' . $splitter . ']', $wrap_text, -1, PREG_SPLIT_NO_EMPTY);

        return implode('', $wrap_text);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('text_columns')) {
    /**
     * text_columns
     *
     * Split a block of strings or text evenly across a number of columns.
     *
     * @param  string $text Text Source
     * @param  int    $cols Number of columns
     *
     * @return array
     */
    function text_columns($text, $cols)
    {
        $col_length = ceil(strlen($text) / $cols) + 3;
        $return = explode("\n", wordwrap(strrev($text), $col_length));

        if (count($return) > $cols) {
            $return[ $cols - 1 ] .= " " . $return[ $cols ];
            unset($return[ $cols ]);
        }

        $return = array_map("strrev", $return);

        return array_reverse($return);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('text_wrap')) {
    /**
     * text_wrap
     *
     * Wrap the given string to a certain chars length and lines.
     *
     * @param  string   $text  Text Source
     * @param  int      $chars 10 means wrap at 40 chars
     * @param  int|bool $lines True or false $lines = false or $lines = 3, means truncate after 3 lines
     *
     * @return string
     */
    function text_wrap($text, $chars = 10, $lines = false)
    {
        # the simple case - return wrapped words
        if ( ! $lines) {
            return wordwrap($text, $chars, "\n");
        }
        # truncate to maximum possible number of characters
        $return = substr($text, 0, $chars * $lines);
        # apply wrapping and return first $lines lines
        $return = wordwrap($return, $chars, "\n");
        preg_match("/(.+\n?){0,$lines}/", $return, $regs);

        return $regs[ 0 ];
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('text_trim')) {
    /**
     * Cuts the given string to a certain length without breaking a word.
     *
     * @param  string $text   Text source
     * @param  int    $limit  number of maximum characters leave remaining
     * @param  string $break  = "." break on dot ending or keep maximum set on ' '
     * @param  string $ending = '...' to display '...' on the end of the trimmed string
     *
     * @return string
     */
    function text_trim($text, $limit, $break = '.', $ending = '.')
    {
        // return with no change if string is shorter than $limit
        if (strlen($text) <= $limit) {
            return $text;
        }
        // is $break present between $limit and the end of the string?
        if (false !== ($breakpoint = strpos($text, $break, $limit))) {
            if ($breakpoint < strlen($text) - 1) {
                $text = substr($text, 0, $breakpoint) . $ending;
            }
        }

        return $text;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_word_limiter')) {
    /**
     * text_word_limiter
     *
     * Limits a string to X number of words.
     *
     * @param    string
     * @param    int
     * @param    string    the end character. Usually an ellipsis
     *
     * @return    string
     */
    function text_word_limiter($str, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($str) === '') {
            return $str;
        }

        preg_match('/^\s*+(?:\S++\s*+){1,' . (int)$limit . '}/', $str, $matches);

        if (strlen($str) === strlen($matches[ 0 ])) {
            $end_char = '';
        }

        return rtrim($matches[ 0 ]) . $end_char;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_character_limiter')) {
    /**
     * text_character_limiter
     *
     * Limits the string based on the character count.  Preserves complete words
     * so the character count may not be exactly as specified.
     *
     * @param    string
     * @param    int
     * @param    string    the end character. Usually an ellipsis
     *
     * @return    string
     */
    function text_character_limiter($string, $n = 500, $end_char = '&#8230;')
    {
        if (mb_strlen($string) < $n) {
            return $string;
        }

        // a bit complicated, but faster than preg_replace with \s+
        $string = preg_replace('/ {2,}/', ' ', str_replace(["\r", "\n", "\t", "\x0B", "\x0C"], ' ', $string));

        if (mb_strlen($string) <= $n) {
            return $string;
        }

        $out = '';
        foreach (explode(' ', trim($string)) as $val) {
            $out .= $val . ' ';

            if (mb_strlen($out) >= $n) {
                $out = trim($out);

                return (mb_strlen($out) === mb_strlen($string)) ? $out : $out . $end_char;
            }
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_censored')) {
    /**
     * text_censored
     *
     * Supply a string and an array of disallowed words and any
     * matched words will be converted to #### or to the replacement
     * word you've submitted.
     *
     * @param    string    the text string
     * @param    string    the array of censoered words
     * @param    string    the optional replacement value
     *
     * @return    string
     */
    function text_censored($string, $censored, $replacement = '')
    {
        if ( ! is_array($censored)) {
            return $string;
        }

        $string = ' ' . $string . ' ';

        // \w, \b and a few others do not match on a unicode character
        // set for performance reasons. As a result words like �ber
        // will not match on a word boundary. Instead, we'll assume that
        // a bad word will be bookeneded by any of these characters.
        $delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

        foreach ($censored as $badword) {
            if ($replacement !== '') {
                $string = preg_replace(
                    "/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/i",
                    "\\1{$replacement}\\3",
                    $string
                );
            } else {
                $string = preg_replace(
                    "/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/ie",
                    "'\\1'.str_repeat('#', strlen('\\2')).'\\3'",
                    $string
                );
            }
        }

        return trim($string);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_highlight_phrase')) {
    /**
     * text_highlight_phrase
     *
     * Highlights a phrase within a text string.
     *
     * @param    string $string    the text string
     * @param    string $phrase    the phrase you'd like to highlight
     * @param    string $tag_open  the openging tag to precede the phrase with
     * @param    string $tag_close the closing tag to end the phrase with
     *
     * @return    string
     */
    function text_highlight_phrase($string, $phrase, $tag_open = '<mark>', $tag_close = '</mark>')
    {
        return ($string !== '' && $phrase !== '')
            ? preg_replace(
                '/(' . preg_quote($phrase, '/') . ')/i' . ('UTF8_ENABLED' ? 'u' : ''),
                $tag_open . '\\1' . $tag_close,
                $string
            )
            : $string;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_word_wrap')) {
    /**
     * text_word_wrap
     *
     * Wraps text at the specified character. Maintains the integrity of words.
     * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
     * will URLs.
     *
     * @param    string $string the text string
     * @param    int    $limit  = 76    the number of characters to wrap at
     *
     * @return    string
     */
    function text_word_wrap($string, $limit = 76)
    {
        // Set the character limit
        is_numeric($limit) OR $limit = 76;

        // Reduce multiple spaces
        $string = preg_replace('| +|', ' ', $string);

        // Standardize newlines
        if (strpos($string, "\r") !== false) {
            $string = str_replace(["\r\n", "\r"], "\n", $string);
        }

        // If the current word is surrounded by {unwrap} tags we'll
        // strip the entire chunk and replace it with a marker.
        $unwrap = [];
        if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $string, $matches)) {
            for ($i = 0, $c = count($matches[ 0 ]); $i < $c; $i++) {
                $unwrap[] = $matches[ 1 ][ $i ];
                $string = str_replace($matches[ 0 ][ $i ], '{{unwrapped' . $i . '}}', $string);
            }
        }

        // Use PHP's native function to do the initial wordwrap.
        // We set the cut flag to FALSE so that any individual words that are
        // too long get left alone. In the next step we'll deal with them.
        $string = wordwrap($string, $limit, "\n", false);

        // Split the string into individual lines of text and cycle through them
        $output = '';
        foreach (explode("\n", $string) as $line) {
            // Is the line within the allowed character count?
            // If so we'll join it to the output and continue
            if (mb_strlen($line) <= $limit) {
                $output .= $line . "\n";
                continue;
            }

            $temp = '';
            while (mb_strlen($line) > $limit) {
                // If the over-length word is a URL we won't wrap it
                if (preg_match('!\[url.+\]|://|www\.!', $line)) {
                    break;
                }

                // Trim the word down
                $temp .= mb_substr($line, 0, $limit - 1);
                $line = mb_substr($line, $limit - 1);
            }

            // If $temp contains data it means we had to split up an over-length
            // word into smaller chunks so we'll add it back to our current line
            if ($temp !== '') {
                $output .= $temp . "\n" . $line . "\n";
            } else {
                $output .= $line . "\n";
            }
        }

        // Put our markers back
        if (count($unwrap) > 0) {
            foreach ($unwrap as $key => $val) {
                $output = str_replace('{{unwrapped' . $key . '}}', $val, $output);
            }
        }

        return $output;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_ellipsis')) {
    /**
     * text_ellipsis
     *
     * This function will strip tags from a string, split it at its max_length and ellipsis
     *
     * @param    string    string to ellipsize
     * @param    int       max length of string
     * @param    mixed     int (1|0) or float, .5, .2, etc for position to split
     * @param    string    ellipsis ; Default '...'
     *
     * @return    string    ellipsis string
     */
    function text_ellipsis($string, $max_length, $position = 1, $ellipsis = '&hellip;')
    {
        // Strip tags
        $string = trim(strip_tags($string));

        // Is the string long enough to ellipsis?
        if (mb_strlen($string) <= $max_length) {
            return $string;
        }

        $beg = mb_substr($string, 0, floor($max_length * $position));
        $position = ($position > 1) ? 1 : $position;

        if ($position === 1) {
            $end = mb_substr($string, 0, -($max_length - mb_strlen($beg)));
        } else {
            $end = mb_substr($string, -($max_length - mb_strlen($beg)));
        }

        return $beg . $ellipsis . $end;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('text_excerpt')) {
    /**
     * text_excerpt
     *
     * Allows to extract a piece of text surrounding a word or phrase.
     *
     * @param   string $string   String to search the phrase
     * @param   string $phrase   Phrase that will be searched for.
     * @param   int    $radius   The amount of characters returned arround the phrase.
     * @param   string $ellipsis Ending that will be appended
     *
     * @return  string
     *
     * If no $phrase is passed, will generate an excerpt of $radius characters
     * from the begining of $text.
     */
    function text_excerpt($string, $phrase = null, $radius = 100, $ellipsis = '...')
    {
        if (isset($phrase)) {
            $phrase_pos = strpos(strtolower($string), strtolower($phrase));
            $phrase_len = strlen($phrase);
        } elseif ( ! isset($phrase)) {
            $phrase_pos = $radius / 2;
            $phrase_len = 1;
        }
        $pre = explode(' ', substr($string, 0, $phrase_pos));
        $pos = explode(' ', substr($string, $phrase_pos + $phrase_len));
        $prev = ' ';
        $post = ' ';
        $count = 0;
        foreach (array_reverse($pre) as $pr => $e) {
            if ((strlen($e) + $count + 1) < $radius) {
                $prev = ' ' . $e . $prev;
            }
            $count = ++$count + strlen($e);
        }
        $count = 0;
        foreach ($pos as $po => $s) {
            if ((strlen($s) + $count + 1) < $radius) {
                $post .= $s . ' ';
            }
            $count = ++$count + strlen($s);
        }
        $ellPre = $phrase ? $ellipsis : '';

        return $ellPre . $prev . $phrase . $post . $ellipsis;
    }
}