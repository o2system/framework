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
 * Class Figure
 *
 * @package O2System\Framework\Libraries\Ui\Contents
 */
class Figure extends Element
{
    public $image;
    public $caption;

    /**
     * Figure::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('figure');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    public function createImage($src = null, $alt = null)
    {
        $this->image = new Image($src, $alt);
        $this->image->attributes->addAttributeClass(['figure-img', 'img-fluid']);

        $this->childNodes->push($this->image);

        return $this->childNodes->last();
    }

    public function createCaption($textContent = null, array $attributes = [])
    {
        $this->caption = new Figure\Caption($attributes);
        $this->caption->attributes->addAttributeClass('figure-caption');

        if (isset($textContent)) {
            $this->textContent->push($textContent);
        }

        $this->childNodes->push($this->caption);

        return $this->childNodes->last();
    }
}