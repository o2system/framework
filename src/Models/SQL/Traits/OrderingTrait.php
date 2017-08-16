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

namespace O2System\Framework\Models\SQL\Traits;

// ------------------------------------------------------------------------

/**
 * Class OrderingTrait
 *
 * @package O2System\Framework\Models\SQL\Traits
 */
trait OrderingTrait
{
    /**
     * Process Row Ordering
     *
     * @access  public
     */
    protected function beforeProcessRowOrdering( array $row, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;

        if ( ! isset( $row[ 'ordering' ] ) ) {
            $row[ 'record_ordering' ] = $this->db->countAllResults( $table ) + 1;
        }
    }
}