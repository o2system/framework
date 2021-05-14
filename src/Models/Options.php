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

namespace O2System\Framework\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Files\Model;

/**
 * Class Options
 * @package O2System\Framework\Models
 */
class Options extends Model
{
    /**
     * Options constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->language->loadFile('options');
    }

    /**
     * @return array
     */
    public function religions(): array
    {
        $religions = [];

        foreach (['ATHEIST', 'HINDU', 'BUDDHA', 'MOSLEM', 'CHRISTIAN', 'CATHOLIC', 'UNDEFINED'] as $religion) {
            $religions[$religion] = $this->language->getLine($religion);
        }

        return $religions;
    }

    /**
     * @return array
     */
    public function genders(): array
    {
        $genders = [];

        foreach (['MALE', 'FEMALE', 'UNDEFINED'] as $gender) {
            $genders[$gender] = $this->language->getLine($gender);
        }

        return $genders;
    }

    /**
     * @return array
     */
    public function maritals(): array
    {
        $maritals = [];

        foreach (['SINGLE', 'MARRIED', 'DIVORCED', 'UNDEFINED'] as $marital) {
            $maritals[$marital] = $this->language->getLine($marital);
        }

        return $maritals;
    }

    /**
     * @return array
     */
    public function bloodTypes(): array
    {
        $bloodTypes = [];

        foreach (['A', 'B', 'AB', 'O', 'UNDEFINED'] as $bloodType) {
            $bloodTypes[$bloodType] = $this->language->getLine($bloodType);
        }

        return $bloodTypes;
    }

    /**
     * @param int $start
     * @param int $end
     * @param string $labelFormat
     * @return array
     */
    public function days(int $start = 1, int $end = 7, string $labelFormat = 'l'): array
    {
        $this->language->loadFile('calendar');

        $year = date('o');
        $week = date('W');

        $days = [];

        for ($i = $start; $i <= $end; $i++) {
            $time = strtotime($year . 'W' . $week . $i);
            $days[strtoupper(date('D', $time))] = $this->language->getLine(
                'CAL_' . strtoupper(date($labelFormat, $time)));
        }

        return $days;
    }

    /**
     * @param int $start
     * @param int $end
     * @param bool $leading
     * @return array
     */
    public function dates(int $start = 1, int $end = 31, bool $leading = true): array
    {
        $dates = [];

        foreach (range($start, $end) as $date) {
            if ($leading) {
                $date = strlen($date) == 1 ? '0' . $date : $date;
            }
            $dates[$date] = $date;
        }

        return $dates;
    }

    /**
     * @param int $start
     * @param int $end
     * @param bool $leading
     * @return array
     */
    public function months(int $start = 1, int $end = 12, bool $leading = true): array
    {
        $this->language->loadFile('calendar');

        $months = [];

        foreach (range($start, $end) as $month) {
            if ($leading) {
                $month = strlen($month) == 1 ? '0' . $month : $month;
            }
            $months[$month] = $this->language->getLine(strtoupper('CAL_' . date('F',
                    strtotime('1-' . $month . '-2000'))));
        }

        return $months;
    }

    /**
     * @param int $start
     * @param null $end
     * @return array
     */
    public function years(int $start = 1900, $end = null): array
    {
        $end = empty($end) ? date('Y') : $end;
        $years = [];

        foreach (range($start, $end) as $year) {
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * @return array
     */
    public function identities(): array
    {
        $identities = [];

        foreach (['UNDEFINED', 'IDENTITY_CARD', 'STUDENT_CARD', 'DRIVER_LICENSE', 'PASSPORT'] as $identity) {
            $identities[$identity] = $this->language->getLine($identity);
        }

        return $identities;
    }

    /**
     * @return array
     */
    public function sizes(): array
    {
        $sizes = [];
        foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size) {
            $sizes[$size] = $size;
        }

        return $sizes;
    }

    /**
     * @return array
     */
    public function boolean(): array
    {
        $boolean = [];
        foreach (['YES', 'NO'] as $bool) {
            $boolean[$bool] = $this->language->getLine('BOOL_' . $bool);
        }

        return $boolean;
    }

    /**
     * @return array
     */
    public function familyRelationships(): array
    {
        $familyRelationships = [];

        foreach ([
                     'PARENT',
                     'CHILD',
                     'SPOUSE',
                     'SIBLING',
                     'GRANDPARENTS',
                     'GRANDCHILD',
                     'PARENTS_SIBLING',
                     'SIBLINGS_CHILD',
                     'AUNTS_UNCLES_CHILD',
                 ] as $relationship
        ) {
            $familyRelationships[$relationship] = $this->language->getLine($relationship);
        }

        return $familyRelationships;
    }

    /**
     * @return array
     */
    public function status(): array
    {
        $statuses = [];

        foreach ([
                     'PUBLISH',
                     'UNPUBLISH',
                     'DRAFT',
                     'ARCHIVED',
                     'DELETED',
                     'LOCKED'
                 ] as $status
        ) {
            $statuses[$status] = $this->language->getLine($status);
        }

        return $statuses;
    }

    // ------------------------------------------------------------------------


    /**
     * @return array
     */
    public function visibilities(): array
    {
        $visibilities = [];

        foreach ([
                     'PUBLIC',
                     'PROTECTED',
                     'PRIVATE',
                     'READONLY',
                     'MEMBERONLY',
                 ] as $visibility
        ) {
            $visibilities[$visibility] = $this->language->getLine($visibility);
        }

        return $visibilities;
    }

    // ------------------------------------------------------------------------

    /**
     * Options::languages
     *
     * @return array
     */
    public function languages(): array
    {
        return $this->language->getOptions();
    }

    // ------------------------------------------------------------------------

    /**
     * Options::languages
     *
     * @return array
     * @throws \Exception
     */
    public function timezones(): array
    {
        language()->loadFile('date');

        $timezoneIdentifiers = \DateTimeZone::listIdentifiers();
        $continent = '';
        $timeszoneIdentifierKey = '';
        $timezones = array();
        $phpTime = \Date("Y-m-d H:i:s");

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            if (preg_match('/^(Europe|America|Asia|Antartica|Arctic|Atlantic|Indian|Pacific)\//', $timezoneIdentifier)) {
                $timezoneIdentifierParts = explode("/", $timezoneIdentifier); //obtain continent,city
                if ($continent != $timezoneIdentifierParts[0]) {
                    $timeszoneIdentifierKey = $timezoneIdentifierParts[0];
                }

                $timezone = new \DateTimeZone($timezoneIdentifier); // Get default system timezone to create a new DateTimeZone object
                $offset = $timezone->getOffset(new \DateTime($phpTime));
                $offsetHours = round(abs($offset) / 3600);
                $offsetString = ($offset < 0 ? '-' : '+');
                if ($offsetHours == 1 or $offsetHours == -1) {
                    $label = language('DATE_HOUR');
                } else {
                    $label = language('DATE_HOURS');
                }

                $city = $timezoneIdentifierParts[1];
                $continent = $timezoneIdentifierParts[0];
                $identifer[$timeszoneIdentifierKey][$timezoneIdentifier] = isset($timezoneIdentifierParts[2]) ? $timezoneIdentifierParts[1] . ' - ' . $timezoneIdentifierParts[2] : $timezoneIdentifierParts[1];
                $timezones[$timeszoneIdentifierKey][$timezoneIdentifier] = $identifer[$timeszoneIdentifierKey][$timezoneIdentifier] . " (" . $offsetString . $offsetHours . " " . $label . ")";
            }
        }

        return $timezones;
    }
}