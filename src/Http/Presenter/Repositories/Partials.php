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

namespace O2System\Framework\Http\Presenter\Repositories;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Partials
 *
 * @package O2System\Framework\Http\Presenter\Repositories
 */
class Partials extends AbstractRepository
{
    /**
     * Partials::hasPartial
     *
     * @param string $partialOffset
     *
     * @return bool
     */
    public function hasPartial($partialOffset)
    {
        return $this->__isset($partialOffset);
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::addPartial
     *
     * @param string $partialOffset
     * @param string $partialFilePath
     */
    public function addPartial($partialOffset, $partialFilePath)
    {
        $this->store($partialOffset, $partialFilePath);
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::get
     *
     * @param string $partial
     *
     * @return mixed
     */
    public function get($partial)
    {
        $partialContent = parent::get($partial);

        if (is_file($partialContent)) {
            parser()->loadFile($partialContent);

            return parser()->parse();
        } elseif (is_string($partialContent)) {
            return $partialContent;
        }

        return null;
    }
}