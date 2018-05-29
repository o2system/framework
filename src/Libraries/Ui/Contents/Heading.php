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

namespace O2System\Framework\Libraries\Ui\Contents;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Heading
 *
 * @package O2System\Framework\Libraries\Ui\Contents
 */
class Heading extends Element
{
    /**
     * Heading::__construct
     *
     * @param string|null $textContentContent
     * @param int         $level
     */
    public function __construct($textContentContent = null, $level = 1, array $attributes = [])
    {
        parent::__construct('h' . $level);

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        if (isset($textContentContent)) {
            $this->textContent->push($textContentContent);
        }
    }

    public function display($level = 1)
    {
        $this->attributes->removeAttributeClass('display-*');
        $this->attributes->addAttributeClass('display-' . (int)$level);

        return $this;
    }
}