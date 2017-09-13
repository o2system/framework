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

namespace O2System\Framework\Models\NoSql\Traits;

// ------------------------------------------------------------------------

/**
 * Class BeforeAfterTrait
 *
 * @package O2System\Framework\Models\NoSql\Traits
 */
trait BeforeAfterTrait
{
    /**
     * List of Before Process Methods
     *
     * @access  protected
     * @type    array
     */
    protected $beforeProcess = [];

    /**
     * List of After Process Methods
     *
     * @access  protected
     * @type    array
     */
    protected $afterProcess = [];

    // ------------------------------------------------------------------------

    /**
     * Before Process
     *
     * Process row data before insert or update
     *
     * @param $row
     * @param $table
     *
     * @access  protected
     * @return  mixed
     */
    protected function beforeProcess( $row, $table )
    {
        if ( ! empty( $this->beforeProcess ) ) {
            foreach ( $this->beforeProcess as $processMethod ) {
                $row = $this->{$processMethod}( $row, $table );
            }
        }

        return $row;
    }

    // ------------------------------------------------------------------------

    /**
     * After Process
     *
     * Runs all after process method actions
     *
     * @access  protected
     * @return  array
     */
    protected function afterProcess()
    {
        $report = [];

        if ( ! empty( $this->afterProcess ) ) {
            foreach ( $this->afterProcess as $processMethod ) {
                $report[ $processMethod ] = $this->{$processMethod}();
            }
        }

        return $report;
    }
    // ------------------------------------------------------------------------
}