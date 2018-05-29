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

namespace O2System\Framework\Libraries\Ui\Components\Card;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components;

/**
 * Class Badge
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Badge extends Components\Badge
{
    const LEFT_BADGE = 0;
    const RIGHT_BADGE = 1;
    const INLINE_BADGE = 2;

    public $position;

    public function __construct($textContent = null, $contextualClass = 'default', $position = self::LEFT_BADGE)
    {
        parent::__construct($textContent, $contextualClass);
        $this->position = $position;
    }
}