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

namespace O2System\Framework\Libraries\Ui\Interfaces;

// ------------------------------------------------------------------------

/**
 * Interface Contextual
 *
 * @package O2System\Framework\Libraries\Ui
 */
interface ContextualInterface
{
    const DEFAULT_CONTEXT = 'default';
    const SUCCESS_CONTEXT = 'success';
    const PRIMARY_CONTEXT = 'primary';
    const SECONDARY_CONTEXT = 'secondary';
    const INFO_CONTEXT = 'info';
    const WARNING_CONTEXT = 'warning';
    const DANGER_CONTEXT = 'danger';
    const LINK_CONTEXT = 'link';
}