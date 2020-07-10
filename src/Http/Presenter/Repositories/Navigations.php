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

use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Navigations
 * @package O2System\Framework\Http\Presenter
 */
class Navigations extends AbstractRepository
{
    /**
     * Navigations::create
     *
     * @param string $name
     *
     * @return Unordered
     */
    public function &create($name)
    {
        $this->offsetSet(camelcase($name), $navigation = new Unordered([
            'id' => dash( 'nav-' . $name)
        ]));

        return $navigation;
    }
}