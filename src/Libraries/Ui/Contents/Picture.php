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
 * Class Picture
 *
 * @package O2System\Framework\Libraries\Ui\Contents
 */
class Picture extends Element
{
    public $source;
    public $image;

    /**
     * Picture::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('picture');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        $this->source = new \O2System\Html\Element('source', 'source');

        if (isset($attributes[ 'srcset' ])) {
            $this->source->attributes->addAttribute('srcset', $attributes[ 'srcset' ]);
            unset($attributes[ 'srcset' ]);
        }

        if (isset($attributes[ 'type' ])) {
            $this->source->attributes->addAttribute('type', $attributes[ 'type' ]);
            unset($attributes[ 'type' ]);
        }

        $this->image = new Image();

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->image->attributes->addAttribute($name, $value);
            }
        }

        $this->childNodes->push($this->image);
    }
}