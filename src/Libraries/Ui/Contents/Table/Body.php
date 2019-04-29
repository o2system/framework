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

namespace O2System\Framework\Libraries\Ui\Contents\Table;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Body
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Table
 */
class Body extends Element
{
    /**
     * Body::__construct
     */
    public function __construct()
    {
        parent::__construct('tbody');
    }

    // ------------------------------------------------------------------------

    /**
     * Body::createRow
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Table\Row
     */
    public function createRow()
    {
        $row = new Row();
        $this->childNodes->push($row);

        return $this->childNodes->last();
    }
}