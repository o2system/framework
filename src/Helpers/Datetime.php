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

if ( ! function_exists('timestamp')) {
    /**
     * timestamp
     *
     * SQL Timestamp
     *
     * @param int|string $timestamp
     *
     * @return string
     * @throws \Exception
     */
    function timestamp($timestamp = null): string
    {
        if ( ! isset($timestamp) OR $timestamp === 'NOW') {
            $timestamp = now();
        } elseif (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('unix_timestamp')) {
    /**
     * unix_timestamp
     *
     * SQL Unix Timestamp
     *
     * @param int|string $timestamp
     *
     * @return string
     * @throws \Exception
     */
    function unix_timestamp($timestamp = null)
    {
        if ( ! isset($timestamp) OR $timestamp === 'NOW') {
            return now();
        } elseif (is_string($timestamp)) {
            return strtotime($timestamp);
        } elseif (is_numeric($timestamp)) {
            return $timestamp;
        }

        return now();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('format_date')) {
    /**
     * format_date
     *
     * Returns string of day, date time or else, depend on type setting, with custom date separator and multi language
     * support
     *
     * @param int|string $timestamp
     * @param string $format
     *
     * @return  string
     * @throws \Exception
     */
    function format_date($timestamp = null, string $format = '%l, %d-%F-%Y %h:%i:%s %a')
    {
        $timestamp = (is_null($timestamp)
            ? now()
            : (is_numeric($timestamp)
                ? $timestamp
                : strtotime(
                    $timestamp
                )));
        $date = parse_date($timestamp);

        $output = $format;

        foreach ($date as $replace => $value) {
            $output = str_ireplace('%' . $replace, $value, $output);
        }

        return $output;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('parse_date')) {
    /**
     * parse_date
     *
     * Parse Date into Array
     *
     * Simple function to take in a date format and return array of associated formats for each date element
     *
     * @param int|string $timestamp
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     * @throws \Exception
     */
    function parse_date($timestamp = null): \O2System\Spl\DataStructures\SplArrayObject
    {
        $timestamp = (is_null($timestamp)
            ? now()
            : (is_numeric($timestamp)
                ? $timestamp
                : strtotime(
                    $timestamp
                )));
        $date_parts = new \O2System\Spl\DataStructures\SplArrayObject(
            [
                't'  => $timestamp,
                'd'  => date('d', $timestamp),
                'D'  => date('D', $timestamp),
                'Y'  => date('Y', $timestamp),
                'y'  => date('y', $timestamp),
                'am' => time_meridiem($timestamp),
                'a'  => date('a', $timestamp),
                'h'  => date('h', $timestamp),
                'H'  => date('H', $timestamp),
                'i'  => date('i', $timestamp),
                's'  => date('s', $timestamp),
                'w'  => date('w', $timestamp),
                'l'  => day_name($timestamp, 'l'),
                'm'  => date('m', $timestamp),
                'n'  => date('n', $timestamp),
                'F'  => month_name($timestamp, 'F'),
                'M'  => month_name($timestamp, 'M'),
                'e'  => string_time_elapsed($timestamp),
            ]
        );

        return $date_parts;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('day_name')) {
    /**
     * day_name
     *
     * Day Name in Languages
     *
     * @param int|string $timestamp
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    function day_name($timestamp = null, string $type = 'D'): array
    {
        $timestamp = (is_null($timestamp)
            ? now()
            : (is_numeric($timestamp)
                ? $timestamp
                : strtotime(
                    $timestamp
                )));

        language()->loadFile('calendar');

        return language()->getLine(strtoupper('CAL_' . date($type, $timestamp)));
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('month_name')) {
    /**
     * month_name
     *
     * Month Name in Languages
     *
     * @param int|string $timestamp
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    function month_name($timestamp = null, string $type = 'M'): string
    {
        $timestamp = (is_null($timestamp)
            ? now()
            : (is_numeric($timestamp)
                ? $timestamp
                : strtotime(
                    $timestamp
                )));
        language()->loadFile('calendar');

        return language()->getLine(strtoupper('CAL_' . date($type, $timestamp)));
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('time_meridian')) {
    /**
     * part_time
     *
     * Part Time in Languages
     *
     * @param int|string $timestamp
     *
     * @return string
     * @throws \Exception
     */
    function time_meridian($timestamp = null): string
    {
        $timestamp = (is_null($timestamp)
            ? now()
            : (is_numeric($timestamp)
                ? $timestamp
                : strtotime(
                    $timestamp
                )));
        $part_time_number = date('G', $timestamp);
        language()->loadFile('date');

        if ($part_time_number >= 5 AND $part_time_number < 11) {
            return language()->getLine('DATE_MORNING');
        } elseif ($part_time_number >= 11 AND $part_time_number < 15) {
            return language()->getLine('DATE_MORNING');
        } elseif ($part_time_number >= 15 AND $part_time_number < 18) {
            return language()->getLine('DATE_DAYTIME');
        } elseif ($part_time_number >= 18 AND $part_time_number < 19) {
            return language()->getLine('DATE_AFTERNOON');
        } elseif ($part_time_number >= 19 AND $part_time_number < 24) {
            return language()->getLine('DATE_NIGHT');
        } elseif ($part_time_number >= 24 AND $part_time_number < 5) {
            return language()->getLine('DATE_MIDNIGHT');
        }
        else {
            return language()->getLine('UNDEFINED');
        }
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('string_time_elapsed')) {
    /**
     * string_time_elapsed
     *
     * Day Date Time in String Format
     *
     * Returns String of Day date time
     *
     * @param   int|string $time Time
     * @param bool $full Full String
     *
     * @return  string
     * @throws \Exception
     */
    function string_time_elapsed($time = null, bool $full = false): string
    {
        language()->loadFile('date');

        $time = is_null($time) ? now() : $time;
        $time = is_numeric($time) ? $time : strtotime($time);

        $now = new DateTime;
        $ago = new DateTime(date('r', $time));
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => language()->getLine('DATE_YEAR'),
            'm' => language()->getLine('DATE_MONTH'),
            'w' => language()->getLine('DATE_WEEK'),
            'd' => language()->getLine('DATE_DAY'),
            'h' => language()->getLine('DATE_HOUR'),
            'i' => language()->getLine('DATE_MINUTE'),
            's' => language()->getLine('DATE_SECOND'),
        ];

        foreach ($string as $key => &$value) {
            if ($diff->$key) {
                $value = $diff->$key . ' ' . $value . ($diff->$key > 1 && language()->getDefaultLocale() === 'en' ? 's' : '');
            } else {
                unset($string[ $key ]);
            }
        }

        if ( ! $full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ',
                $string) . ' ' . language()->getLine('DATE_AGO') : language()->getLine('DATE_JUST_NOW');

    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('dates_between')) {
    /**
     * dates_between
     *
     * Here is a simple function that gets all the dates between 2 given dates and returns an array (including the dates
     * specified)
     *
     * @example
     * dates_between('2001-12-28', '2002-01-01');
     *
     * @param   int|string $start_date Start Date
     * @param   int|string $end_date   End Date
     *
     * @return  array
     */
    function dates_between($start_date, $end_date, $format = 'Y-m-d'): array
    {
        $day = 60 * 60 * 24;
        $start_date = (! is_numeric($start_date) ? strtotime($start_date) : $start_date);
        $end_date = (! is_numeric($end_date) ? strtotime($end_date) : $end_date);

        $days_diff = round(
            ($end_date - $start_date) / $day
        ); // Unix time difference devided by 1 day to get total days in between

        $dates_array = [];

        if ($format == 'time') {
            $dates_array[] = $start_date;
        } else {
            $dates_array[] = date($format, $start_date);
        }

        for ($x = 1; $x < $days_diff; $x++) {
            if ($format == 'time') {
                $dates_array[] = $start_date + ($day * $x);
            } else {
                $dates_array[] = date($format, ($start_date + ($day * $x)));
            }
        }

        if ($format == 'time') {
            $dates_array[] = $end_date;
        } else {
            $dates_array[] = date($format, $end_date);
        }

        return $dates_array;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('time_range')) {
    /**
     * time_range
     *
     * Returns Array of Time Range Options in 15 Minutes Step
     *
     * @param int $mode 12|24 Hour Mode
     * @param int $step Minutes Step
     *
     * @return  array
     */
    function time_range(int $mode = 24, int $step = 15): array
    {
        $time = [];
        $minutes = range(0, (60 - $step), $step);
        for ($i = 0; $i <= 23; $i++) {
            $hour = (strlen($i) == 1 ? '0' . $i : $i);
            foreach ($minutes as $minute) {
                $hours = $hour . ':' . (strlen($minute) == 1 ? '0' . $minute : $minute);
                $time_12 = date("h:i a", strtotime($hours));
                $time_24 = $hours;
                if ($mode == 12) {
                    $time[ $time_12 ] = $time_12;
                } elseif ($mode == 24) {
                    $time[ $time_24 ] = $time_24;
                }
            }
        }

        return $time;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('calculate_days')) {
    /**
     * calculate_days
     *
     * Returns amount of weeks
     *
     * @param   int|string $start_date Start Date
     * @param   int|string $end_date   End Date
     * @param string $hour       Hour
     *
     * @return  int
     */
    function calculate_days($start_date, $end_date, string $hour = '12:00:00 am'): int
    {
        list($start_date, $end_date) = getDate($start_date, $end_date, $hour);

        $hours = 24 * 60 * 60; // Hours in a day
        $time = $end_date - $start_date;

        return round($time / $hours);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('calculate_weeks')) {
    /**
     * calculate_weeks
     *
     * Returns amount of weeks
     *
     * @param   int|string $start_date Start Date
     * @param   int|string $end_date   End Date
     * @param string $hour       Hour
     *
     * @return  int
     */
    function calculate_weeks($start_date, $end_date, string $hour = '12:00:00 am'): int
    {
        list($start_date, $end_date) = getDate($start_date, $end_date, $hour);

        $hours = 24 * 60 * 60 * 7; // Hours in a day
        $time = $end_date - $start_date;

        return floor($time / $hours);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param string $hour
     * @return array
     */
    function getDate($start_date, $end_date, string $hour): array
    {
        $start_date = (is_numeric($start_date) ? date('d-m-Y', $start_date) : $start_date);
        $end_date = (is_numeric($end_date) ? date('d-m-Y', $end_date) : $end_date);
        $hour = (is_numeric($hour) ? date('h:i:s a', $hour) : $hour);

        $start_date = $start_date . ' ' . $hour;
        $end_date = $end_date . ' ' . $hour;

        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        return array($start_date, $end_date);
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('is_weekend')) {
    /**
     * is_weekend
     *
     * Validate is the date is a weekend
     *
     * @param   int|string $date Date
     *
     * @return  bool
     */
    function is_weekend($date): bool
    {
        $date = (! is_numeric($date) ? strtotime(str_replace('/', '-', $date)) : $date);
        $date = date('D', $date);

        if ($date == 'Sat' OR $date == 'Sun') {
            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('is_weekday')) {
    /**
     * is_weekday
     *
     * Validate is the date is a week day
     *
     * @param   int|string $date Date
     *
     * @return  bool
     */
    function is_weekday($date): bool
    {
        $date = (! is_numeric($date) ? strtotime(str_replace('/', '-', $date)) : $date);
        $date = date('D', $date);

        if ( ! in_array($date, ['Sat', 'Sun'])) {
            return true;
        }

        return false;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('get_age')) {
    /**
     * get_age
     *
     * Gets the age of an individual
     *
     * @param int|string $birthday
     * @param string $return Return value days|months|years
     *
     * @return int
     * @throws \Exception
     */
    function get_age($birthday, string $return = 'years'): int
    {
        $birthday = (! is_numeric($birthday) ? strtotime(str_replace('/', '-', $birthday)) : $birthday);

        $birthday = new DateTime(date('Y-m-d', $birthday));
        $now = new DateTime(date('Y-m-d'));
        $interval = $birthday->diff($now);

        $available = [
            'years'   => 'y',
            'months'  => 'm',
            'hours'   => 'h',
            'minutes' => 'i',
            'seconds' => 's',
        ];

        if (array_key_exists($return, $available)) {
            return $interval->{$available[ $return ]};
        } elseif (isset($interval->{$return})) {
            return $interval->{$return};
        }

        return $interval;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('get_tenure')) {
    /**
     * get_tenure
     *
     * Gets tenure from start date and end date
     *
     * @param int|string      $date_start
     * @param int|string|null $date_end
     *
     * @return string
     * @throws \Exception
     */
    function get_tenure($date_start, $date_end = null): string
    {
        language()->loadFile('date');

        $date_start = new \DateTime($date_start);

        if (empty($date_end)) {
            $date_end = new \DateTime($date_end);
        } else {
            $date_end = new DateTime(date('Y-m-d'));
        }

        $dateInterval = $date_start->diff($date_end);

        return strtolower($dateInterval->format(
            '%y ' . language()->getLine('DATE_YEARS') .
            ' &mdash; %m ' . language()->getLine('DATE_MONTHS') .
            ' &mdash; %d ' . language()->getLine('DATE_DAYS')));
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('time_breakdown')) {
    /**
     * time_breakdown
     *
     * Breakdown a timestamp into an array of days, months, etc since the current time
     *
     * @param int|string $time
     *
     * @return array
     */
    function time_breakdown($time): array
    {
        if ( ! is_numeric($time)) {
            $time = strtotime($time);
        }
        $currentTime = time();
        $periods = [
            'years'   => 31556926,
            'months'  => 2629743,
            'weeks'   => 604800,
            'days'    => 86400,
            'hours'   => 3600,
            'minutes' => 60,
            'seconds' => 1,
        ];
        $durations = [
            'years'   => 0,
            'months'  => 0,
            'weeks'   => 0,
            'days'    => 0,
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        ];
        if ($time) {
            $seconds = $currentTime - $time;
            if ($seconds <= 0) {
                return $durations;
            }
            foreach ($periods as $period => $seconds_in_period) {
                if ($seconds >= $seconds_in_period) {
                    $durations[ $period ] = floor($seconds / $seconds_in_period);
                    $seconds -= $durations[ $period ] * $seconds_in_period;
                }
            }
        }

        return $durations;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('sec2hms')) {
    /**
     * sec2hms
     *
     * Convert second to Miliseconds
     *
     * @param int $num_secs
     *
     * @return string
     */
    function sec2hms(int $num_secs): string
    {
        $str = '';
        $hours = intval(intval($num_secs) / 3600);
        $str .= $hours . ':';
        $minutes = intval(((intval($num_secs) / 60) % 60));
        if ($minutes < 10) {
            $str .= '0';
        }
        $str .= $minutes . ':';
        $seconds = intval(intval(($num_secs % 60)));
        if ($seconds < 10) {
            $str .= '0';
        }
        $str .= $seconds;

        return $str;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('add_time_duration')) {
    /**
     * add_time_duration
     *
     * Add Time Duration
     *
     * @param int|string $start_time Time or Date
     * @param int $duration
     * @param string $return Return value|date
     *
     * @return mixed
     * @throws Exception
     */
    function add_time_duration($start_time, int $duration, string $return = 'time')
    {
        $start_time = (! is_numeric($start_time) ? strtotime($start_time) : $start_time);
        $duration = $duration * 60 * 60; // (x) hours * 60 minutes * 60 seconds

        $add_time = $start_time + $duration;

        if ($return === 'time') {
            return $add_time;
        } else {
            return format_date($add_time, $return);
        }
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('calculate_hours')) {
    /**
     * calculate_hours
     *
     * Calculate Hours
     *
     * @param int|string $start_time Time or Date
     * @param int|string $end_time   Time or Date
     * @param string $return     Return value time|hours
     *
     * @return mixed
     */
    function calculate_hours($start_time, $end_time, string $return = 'time')
    {
        $start_time = (! is_numeric($start_time) ? strtotime($start_time) : $start_time);
        $end_time = (! is_numeric($end_time) ? strtotime($end_time) : $end_time);

        // Times Difference
        $difference = $end_time - $start_time;

        // Hours
        $hours = $difference / 3600;

        // Minutes
        $minutes = ($hours - floor($hours)) * 60;

        $hours = ($minutes != 0 ? $hours - 1 : $hours);

        // Final
        $final_hours = round($hours, 0);
        $final_minutes = round($minutes);

        if ($return === 'time') {
            $final_hours = ($final_hours < 10 ? '0' . $final_hours : $final_hours);
            $final_minutes = ($final_minutes < 10 ? '0' . $final_minutes : $final_minutes);

            return $final_hours . ':' . $final_minutes;
        } elseif ($return === 'hours') {
            return $final_hours + ($final_minutes / 60);
        }
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('time_difference')) {
    /**
     * time_difference
     *
     * Gets the time difference
     *
     * @param int|string $start_time Time or Date
     * @param int|string $end_time   Time or Date
     * @param string $return     Return value array|object
     *
     * @return mixed
     */
    function time_difference($start_time, $end_time, string $return = 'array')
    {
        $start_time = (! is_numeric($start_time) ? strtotime($start_time) : $start_time);
        $end_time = (! is_numeric($end_time) ? strtotime($end_time) : $end_time);

        // Times Difference
        $difference = $end_time - $start_time;
        $result = format_time(abs($difference));

        if ($return == 'array') {
            return $result;
        } else {
            return implode(':', $result);
        }
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('weeks_in_month')) {
    /**
     * weeks_in_month
     *
     * Gets num of weeks in a month.
     *
     * @param int|null $month
     * @param int|null $year
     *
     * @return int
     */
    function weeks_in_month(int $month = null, int $year = null)
    {
        // Start Date in Month
        $start_date_month = mktime(0, 0, 0, $month, 1, $year);
        $start_week_month = (int)date('W', $start_date_month);

        $amount_day = days_in_month($month, $year);

        // Finish Date in onth
        $finish_date_month = mktime(0, 0, 0, $month, $amount_day, $year);
        $finish_week_month = (int)date('W', $finish_date_month);

        $amount_week = $finish_week_month - $start_week_month + 1;

        return $amount_week;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('monday_of_week')) {
    /**
     * monday_of_week
     *
     * Gets the date of monday of the week.
     *
     * @param int $week_number
     * @param int|null $year
     *
     * @return int
     */
    function monday_of_week(int $week_number, int $year = null): int
    {
        $year = is_null($year) ? date('Y') : $year;

        $new_year_date = mktime(0, 0, 0, 1, 1, $year);
        $first_monday_date = 7 + 1 - date("w", mktime(0, 0, 0, 1, 1, $year));
        $dates_from_first_monday = 7 * $week_number;
        $second_from_first_monday = 60 * 60 * 24 * ($first_monday_date + $dates_from_first_monday);
        $monday_day_of_week = $new_year_date + $second_from_first_monday;
        $date_of_monday_day_of_week = 0 + date("j", $monday_day_of_week);

        return $date_of_monday_day_of_week;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('week_number_of_month')) {
    /**
     * week_number_of_month
     *
     * Gets the week Number of a month.
     *
     * @param int|null $date  Date Number
     * @param int|null $month Month Number
     * @param int|null $year  Year Number
     *
     * @return int
     */
    function week_number_of_month(int $date = null, int $month = null, int $year = null): int
    {
        $month = is_null($month) ? date('m') : $month;
        $year = is_null($year) ? date('Y') : $year;

        // Start Date in Month
        $start_date_month = mktime(0, 0, 0, $month, 1, $year);
        $start_week_month = (int)date('W', $start_date_month);

        // Date Search
        $date_search = mktime(0, 0, 0, $month, $date, $year);
        $date_week_search = (int)date('W', $date_search);

        $number_of_week = $date_week_search - $start_week_month + 1;

        return $number_of_week;
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('format_time')) {
    /**
     * format_time
     *
     * Format Time from seconds
     *
     * @param int $seconds
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    function format_time(int $seconds): \O2System\Spl\DataStructures\SplArrayObject
    {
        @$days = floor($seconds / 86400);
        if ($days > 0) {
            $seconds -= $days * 86400;
        }

        @$years = floor($days / 365);
        @$months = floor($days / 30);

        @$hours = floor($seconds / 3600);
        if ($days > 0 || $hours > 0) {
            $seconds -= $hours * 3600;
        }

        @$minutes = floor($seconds / 60);
        if ($days > 0 || $hours > 0 || $minutes > 0) {
            $seconds -= $minutes * 60;
        }

        $format[ 'days' ] = $days;
        $format[ 'years' ] = $years;
        $format[ 'months' ] = $months;
        $format[ 'hours' ] = $hours;
        $format[ 'minutes' ] = $minutes;
        $format[ 'seconds' ] = $seconds;

        return new \O2System\Spl\DataStructures\SplArrayObject($format);
    }
}
// ------------------------------------------------------------------------

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package      CodeIgniter
 * @author       EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license      http://opensource.org/licenses/MIT	MIT License
 * @link         http://codeigniter.com
 * @since        Version 1.0.0
 * @filesource
 */

/**
 * CodeIgniter Date Helpers
 *
 * @package        CodeIgniter
 * @subpackage     Helpers
 * @category       Helpers
 * @author         EllisLab Dev Team
 * @link           http://codeigniter.com/user_guide/helpers/date_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('now')) {
    /**
     * now
     *
     * Get "now" time
     *
     * Returns time() based on the timezone parameter or on the
     * "time_reference" setting
     *
     * @param  $timezone string|null
     *
     * @return  int
     * @throws \Exception
     */
    function now(string $timezone = null): int
    {
        if (empty($timezone)) {
            $timezone = config()->getItem('datetime')->timezone;
        }

        if ($timezone === 'local' OR $timezone === date_default_timezone_get()) {
            return time();
        }

        $datetime = new DateTime('now', new DateTimeZone($timezone));
        sscanf($datetime->format('j-n-Y G:i:s'), '%d-%d-%d %d:%d:%d', $day, $month, $year, $hour, $minute, $second);

        return mktime($hour, $minute, $second, $month, $day, $year);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('mdate')) {
    /**
     * mdate
     *
     * Convert MySQL Style Datecodes
     *
     * This function is identical to PHPs date() function,
     * except that it allows date codes to be formatted using
     * the MySQL style, where each code letter is preceded
     * with a percent sign:  %Y %m %d etc...
     *
     * The benefit of doing dates this way is that you don't
     * have to worry about escaping your text letters that
     * match the date codes.
     *
     * @param    string
     * @param    int
     *
     * @return    int
     * @throws \Exception
     */
    function mdate($date = '', $time = '')
    {
        if ($date === '') {
            return '';
        } elseif (empty($time)) {
            $time = now();
        }

        $date = str_replace(
            '%\\',
            '',
            preg_replace('/([a-z]+?){1}/i', '\\\\\\1', $date)
        );

        return date($date, $time);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('timespan')) {
    /**
     * timespan
     *
     * Returns a span of seconds in this format:
     *    10 days 14 hours 36 minutes 47 seconds
     *
     * @param int $seconds a number of seconds
     * @param int|null $time    Unix timestamp
     * @param int $units   a number of display units
     *
     * @return    string
     */
    function timespan(int $seconds = 1, int $time = null, int $units = 7)
    {
        language()->loadFile('date');

        is_numeric($seconds) OR $seconds = 1;
        is_numeric($time) OR $time = time();
        is_numeric($units) OR $units = 7;

        $seconds = ($time <= $seconds) ? 1 : $time - $seconds;

        $str = [];
        $years = floor($seconds / 31557600);

        if ($years > 0) {
            $str[] = $years . ' ' . language()->getLine($years > 1 ? 'DATE_YEARS' : 'DATE_YEAR');
        }

        $seconds -= $years * 31557600;
        $months = floor($seconds / 2629743);

        if (count($str) < $units && ($years > 0 OR $months > 0)) {
            if ($months > 0) {
                $str[] = $months . ' ' . language()->getLine($months > 1 ? 'DATE_MONTHS' : 'DATE_MONTH');
            }

            $seconds -= $months * 2629743;
        }

        $weeks = floor($seconds / 604800);

        if (count($str) < $units && ($years > 0 OR $months > 0 OR $weeks > 0)) {
            if ($weeks > 0) {
                $str[] = $weeks . ' ' . language()->getLine($weeks > 1 ? 'DATE_WEEKS' : 'DATE_WEEK');
            }

            $seconds -= $weeks * 604800;
        }

        $days = floor($seconds / 86400);

        if (count($str) < $units && ($months > 0 OR $weeks > 0 OR $days > 0)) {
            if ($days > 0) {
                $str[] = $days . ' ' . language()->getLine($days > 1 ? 'DATE_DAYS' : 'DATE_DAY');
            }

            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);

        if (count($str) < $units && ($days > 0 OR $hours > 0)) {
            if ($hours > 0) {
                $str[] = $hours . ' ' . language()->getLine($hours > 1 ? 'DATE_HOURS' : 'DATE_HOUR');
            }

            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);

        if (count($str) < $units && ($days > 0 OR $hours > 0 OR $minutes > 0)) {
            if ($minutes > 0) {
                $str[] = $minutes . ' ' . language()->getLine($minutes > 1 ? 'DATE_MINUTES' : 'DATE_MINUTE');
            }

            $seconds -= $minutes * 60;
        }

        if (count($str) === 0) {
            $str[] = $seconds . ' ' . language()->getLine($seconds > 1 ? 'DATE_SECONDS' : 'DATE_SECOND');
        }

        return implode(', ', $str);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('days_in_month')) {
    /**
     * days_in_month
     *
     * Number of days in a month
     *
     * Takes a month/year as input and returns the number of days
     * for the given month/year. Takes leap years into consideration.
     *
     * @param int $month a numeric month
     * @param int|null $year  a numeric year
     *
     * @return    int
     */
    function days_in_month(int $month = 0, int $year = null): int
    {
        if ($month < 1 or $month > 12) {
            return 0;
        } elseif ( ! is_numeric($year) or strlen($year) !== 4) {
            $year = date('Y');
        }

        if (defined('CAL_GREGORIAN')) {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($year >= 1970) {
            return (int)date('t', mktime(12, 0, 0, $month, 1, $year));
        }

        if ($month == 2) {
            if ($year % 400 === 0 OR ($year % 4 === 0 && $year % 100 !== 0)) {
                return 29;
            }
        }

        $days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        return $days_in_month[ $month - 1 ];
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('local_to_gmt')) {
    /**
     * local_to_gmt
     *
     * Converts a local Unix timestamp to GMT
     *
     * @param int|null $time Unix timestamp
     *
     * @return int
     */
    function local_to_gmt(int $time = null): int
    {
        $time = is_numeric($time) ? $time : time();

        return mktime(
            gmdate('G', $time),
            gmdate('i', $time),
            gmdate('s', $time),
            gmdate('n', $time),
            gmdate('j', $time),
            gmdate('Y', $time)
        );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('gmt_to_local')) {
    /**
     * gmt_to_local
     *
     * Converts GMT time to a localized value
     *
     * Takes a Unix timestamp (in GMT) as input, and returns
     * at the local value based on the timezone and DST setting
     * submitted
     *
     * @param int|null $time     Unix timestamp
     * @param string $timezone timezone
     * @param bool $dst      whether DST is active
     *
     * @return    int
     * @throws \Exception
     */
    function gmt_to_local(int $time = null, string $timezone = 'UTC', bool $dst = false)
    {
        $time = is_numeric($time) ? $time : now();

        if ($time === '') {
            return now();
        }

        $time += timezones($timezone) * 3600;

        return ($dst === true) ? $time + 3600 : $time;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('mysql_to_unix')) {
    /**
     * mysql_to_unix
     *
     * Converts a MySQL Timestamp to Unix
     *
     * @param int|string $time MySQL timestamp YYYY-MM-DD HH:MM:SS
     *
     * @return  int    Unix timestamp
     */
    function mysql_to_unix($time = ''): int
    {
        // We'll remove certain characters for backward compatibility
        // since the formatting changed with MySQL 4.1
        // YYYY-MM-DD HH:MM:SS

        $time = str_replace(['-', ':', ' '], '', $time);

        // YYYYMMDDHHMMSS
        return mktime(
            substr($time, 8, 2),
            substr($time, 10, 2),
            substr($time, 12, 2),
            substr($time, 4, 2),
            substr($time, 6, 2),
            substr($time, 0, 4)
        );
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('unix_to_human')) {
    /**
     * unix_to_human
     *
     * Unix to "Human"
     *
     * Formats Unix timestamp to the following prototype: 2006-08-21 11:35 PM
     *
     * @param int|null $time    Unix timestamp
     * @param bool $seconds whether to show seconds
     * @param string $format  format: us or euro
     *
     * @return    string
     * @throws \Exception
     */
    function unix_to_human(int $time = null, bool $seconds = false, string $format = 'us'): string
    {
        $time = is_null($time) ? now() : $time;

        $result = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time) . ' ';

        if ($format === 'us') {
            $result .= date('h', $time) . ':' . date('i', $time);
        } else {
            $result .= date('H', $time) . ':' . date('i', $time);
        }

        if ($seconds) {
            $result .= ':' . date('s', $time);
        }

        if ($format === 'us') {
            return $result . ' ' . date('A', $time);
        }

        return $result;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('human_to_unix')) {
    /**
     * human_to_unix
     *
     * Convert "human" date to GMT
     *
     * Reverses the above process
     *
     * @param string $date format: us or euro
     *
     * @return int
     */
    function human_to_unix(string $date = '')
    {
        if ($date === '') {
            return false;
        }

        $date = preg_replace('/\040+/', ' ', trim($date));

        if ( ! preg_match(
            '/^(\d{2}|\d{4})\-[0-9]{1,2}\-[0-9]{1,2}\s[0-9]{1,2}:[0-9]{1,2}(?::[0-9]{1,2})?(?:\s[AP]M)?$/i',
            $date
        )
        ) {
            return false;
        }

        sscanf($date, '%d-%d-%d %s %s', $year, $month, $day, $time, $ampm);
        sscanf($time, '%d:%d:%d', $hour, $min, $sec);
        isset($sec) OR $sec = 0;

        if (isset($ampm)) {
            $ampm = strtolower($ampm);

            if ($ampm[ 0 ] === 'p' && $hour < 12) {
                $hour += 12;
            } elseif ($ampm[ 0 ] === 'a' && $hour === 12) {
                $hour = 0;
            }
        }

        return mktime($hour, $min, $sec, $month, $day, $year);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('nice_date')) {
    /**
     * nice_date
     *
     * Turns many "reasonably-date-like" strings into something
     * that is actually useful. This only works for dates after unix epoch.
     *
     * @param string $bad_date The terribly formatted date-like string
     * @param string|null $format   Date format to return (same as php date function)
     *
     * @return string
     */
    function nice_date(string $bad_date = '', string $format = null): string
    {
        if (empty($bad_date)) {
            return 'Unknown';
        } elseif (empty($format)) {
            $format = 'U';
        }

        // Date like: YYYYMM
        if (preg_match('/^\d{6}$/i', $bad_date)) {
            if (in_array(substr($bad_date, 0, 2), ['19', '20'])) {
                $year = substr($bad_date, 0, 4);
                $month = substr($bad_date, 4, 2);
            } else {
                $month = substr($bad_date, 0, 2);
                $year = substr($bad_date, 2, 4);
            }

            return date($format, strtotime($year . '-' . $month . '-01'));
        }

        // Date Like: YYYYMMDD
        if (preg_match('/^(\d{2})\d{2}(\d{4})$/i', $bad_date, $matches)) {
            return date($format, strtotime($matches[ 1 ] . '/01/' . $matches[ 2 ]));
        }

        // Date Like: MM-DD-YYYY __or__ M-D-YYYY (or anything in between)
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/i', $bad_date, $matches)) {
            return date($format, strtotime($matches[ 3 ] . '-' . $matches[ 1 ] . '-' . $matches[ 2 ]));
        }

        // Any other kind of string, when converted into UNIX time,
        // produces "0 seconds after epoc..." is probably bad...
        // return "Invalid Date".
        if (date('U', strtotime($bad_date)) === '0') {
            return 'Invalid Date';
        }

        // It's probably a valid-ish date format already
        return date($format, strtotime($bad_date));
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('timezones')) {
    /**
     * timezones
     *
     * Returns an array of timezones. This is a helper function
     * for various other ones in this library
     *
     * @param string $timezone timezone
     *
     * @return string|array
     */
    function timezones(string $timezone = ''): ?string
    {
        // Note: Don't change the order of these even though
        // some items appear to be in the wrong order

        $zones = [
            'UM12'   => -12,
            'UM11'   => -11,
            'UM10'   => -10,
            'UM95'   => -9.5,
            'UM9'    => -9,
            'UM8'    => -8,
            'UM7'    => -7,
            'UM6'    => -6,
            'UM5'    => -5,
            'UM45'   => -4.5,
            'UM4'    => -4,
            'UM35'   => -3.5,
            'UM3'    => -3,
            'UM2'    => -2,
            'UM1'    => -1,
            'UTC'    => 0,
            'UP1'    => +1,
            'UP2'    => +2,
            'UP3'    => +3,
            'UP35'   => +3.5,
            'UP4'    => +4,
            'UP45'   => +4.5,
            'UP5'    => +5,
            'UP55'   => +5.5,
            'UP575'  => +5.75,
            'UP6'    => +6,
            'UP65'   => +6.5,
            'UP7'    => +7,
            'UP8'    => +8,
            'UP875'  => +8.75,
            'UP9'    => +9,
            'UP95'   => +9.5,
            'UP10'   => +10,
            'UP105'  => +10.5,
            'UP11'   => +11,
            'UP115'  => +11.5,
            'UP12'   => +12,
            'UP1275' => +12.75,
            'UP13'   => +13,
            'UP14'   => +14,
        ];

        if ($timezone === '') {
            return $zones;
        }

        return $zones[$timezone] ?? 0;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('date_range_unix')) {
    /**
     * date_range_unix
     *
     * Date range
     *
     * Returns a list of dates within a specified period.
     *
     * @param int|null $unix_start unix_start    UNIX timestamp of period start date
     * @param int|null $mixed      unix_end|days    UNIX timestamp of period end date
     *                              or interval in days.
     * @param    mixed  $is_unix    is_unix        Specifies whether the second parameter
     *                              is a UNIX timestamp or a day interval
     *                              - TRUE or 'unix' for a timestamp
     *                              - FALSE or 'days' for an interval
     * @param string $format     date_format    Browser date format, same as in date()
     *
     * @return   bool|array
     * @throws \Exception
     */
    function date_range_unix(int $unix_start = null, int $mixed = null, $is_unix = true, string $format = 'Y-m-d'): ?array
    {
        if (is_null($unix_start) or is_null($mixed) or empty($format)) {
            return false;
        }

        $is_unix = ! ( ! $is_unix OR $is_unix === 'days');

        // Validate input and try strtotime() on invalid timestamps/intervals, just in case
        if (( ! ctype_digit((string)$unix_start) && ($unix_start = @strtotime($unix_start)) === false)
            OR ( ! ctype_digit((string)$mixed) && ($is_unix === false OR ($mixed = @strtotime(
                        $mixed
                    )) === false))
            OR ($is_unix === true && $mixed < $unix_start)
        ) {
            return false;
        }

        if ($is_unix && ($unix_start == $mixed OR date($format, $unix_start) === date($format, $mixed))) {
            return [date($format, $unix_start)];
        }

        $range = [];

        /* NOTE: Even though the DateTime object has many useful features, it appears that
         *	 it doesn't always handle properly timezones, when timestamps are passed
         *	 directly to its constructor. Neither of the following gave proper results:
         *
         *		new DateTime('<timestamp>')
         *		new DateTime('<timestamp>', '<timezone>')
         *
         *	 --- available in PHP 5.3:
         *
         *		DateTime::createFromFormat('<format>', '<timestamp>')
         *		DateTime::createFromFormat('<format>', '<timestamp>', '<timezone')
         *
         *	 ... so we'll have to set the timestamp after the object is instantiated.
         *	 Furthermore, in PHP 5.3 we can use DateTime::setTimestamp() to do that and
         *	 given that we have UNIX timestamps - we should use it.
        */
        $from = new DateTime();

        if (is_php('5.3')) {
            $from->setTimestamp($unix_start);
            if ($is_unix) {
                $arg = new DateTime();
                $arg->setTimestamp($mixed);
            } else {
                $arg = (int)$mixed;
            }

            $period = new DatePeriod($from, new DateInterval('P1D'), $arg);
            foreach ($period as $date) {
                $range[] = $date->format($format);
            }

            /* If a period end date was passed to the DatePeriod constructor, it might not
             * be in our results. Not sure if this is a bug or it's just possible because
             * the end date might actually be less than 24 hours away from the previously
             * generated DateTime object, but either way - we have to append it manually.
             */
            if ( ! is_int($arg) && $range[ count($range) - 1 ] !== $arg->format($format)) {
                $range[] = $arg->format($format);
            }

            return $range;
        }

        $from->setDate(date('Y', $unix_start), date('n', $unix_start), date('j', $unix_start));
        $from->setTime(date('G', $unix_start), date('i', $unix_start), date('s', $unix_start));
        if ($is_unix) {
            $arg = new DateTime();
            $arg->setDate(date('Y', $mixed), date('n', $mixed), date('j', $mixed));
            $arg->setTime(date('G', $mixed), date('i', $mixed), date('s', $mixed));
        } else {
            $arg = (int)$mixed;
        }
        $range[] = $from->format($format);

        if (is_int($arg)) // Day intervals
        {
            do {
                $from->modify('+1 day');
                $range[] = $from->format($format);
            } while (--$arg > 0);
        } else // end date UNIX timestamp
        {
            for ($from->modify('+1 day'), $end_check = $arg->format('Ymd'); $from->format(
                'Ymd'
            ) < $end_check; $from->modify('+1 day')
            ) {
                $range[] = $from->format($format);
            }

            // Our loop only appended dates prior to our end date
            $range[] = $arg->format($format);
        }

        return $range;
    }
}