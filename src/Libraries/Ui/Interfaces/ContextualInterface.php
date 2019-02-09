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

namespace O2System\Framework\Libraries\Ui\Interfaces;

// ------------------------------------------------------------------------

/**
 * Interface Contextual
 *
 * @package O2System\Framework\Libraries\Ui
 */
interface ContextualInterface
{
    /**
     * ContextualInterface::DEFAULT_CONTEXT
     *
     * @var string
     */
    const DEFAULT_CONTEXT = 'default';

    /**
     * ContextualInterface::SUCCESS_CONTEXT
     *
     * @var string
     */
    const SUCCESS_CONTEXT = 'success';

    /**
     * ContextualInterface::PRIMARY_CONTEXT
     *
     * @var string
     */
    const PRIMARY_CONTEXT = 'primary';

    /**
     * ContextualInterface::SECONDARY_CONTEXT
     *
     * @var string
     */
    const SECONDARY_CONTEXT = 'secondary';

    /**
     * ContextualInterface::INFO_CONTEXT
     *
     * @var string
     */
    const INFO_CONTEXT = 'info';

    /**
     * ContextualInterface::WARNING_CONTEXT
     *
     * @var string
     */
    const WARNING_CONTEXT = 'warning';

    /**
     * ContextualInterface::DANGER_CONTEXT
     *
     * @var string
     */
    const DANGER_CONTEXT = 'danger';

    /**
     * ContextualInterface::LINK_CONTEXT
     *
     * @var string
     */
    const LINK_CONTEXT = 'link';
}