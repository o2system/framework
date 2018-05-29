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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Partials
 *
 * @package O2System\Framework\Http\Presenter
 */
class Partials extends AbstractRepository
{
    public function hasPartial($partialOffset)
    {
        return $this->__isset($partialOffset);
    }

    public function addPartial($partialOffset, $partialFilePath)
    {
        $this->store($partialOffset, $partialFilePath);
    }

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