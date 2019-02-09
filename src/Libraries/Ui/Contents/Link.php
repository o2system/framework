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

namespace O2System\Framework\Libraries\Ui\Contents;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\PopoverSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TooltipSetterTrait;

/**
 * Class Link
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Link extends Element
{
    use PopoverSetterTrait;
    use TooltipSetterTrait;

    /**
     * Link::$icon
     *
     * @var \O2System\Framework\Libraries\Ui\Contents\Icon
     */
    public $icon;

    // ------------------------------------------------------------------------

    /**
     * Link::__construct
     *
     * @param Element|string|null $label
     * @param string|null $href
     */
    public function __construct($label = null, $href = null)
    {
        parent::__construct('a');

        if (isset($label)) {

            if ($label instanceof Element) {
                $this->childNodes->prepend($label);
                $this->entity->setEntityName($label->entity->getEntityName());
            } else {
                $this->textContent->prepend($label);
                $this->entity->setEntityName($label);
            }
        }

        if (isset($href)) {
            $this->setAttributeHref($href);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Link::setAttributeHref
     *
     * @param string $href
     *
     * @return static
     */
    public function setAttributeHref($href)
    {
        if (strpos($href, 'http') !== false) {
            $this->attributes->addAttribute('href', $href);
        } elseif (strpos($href, 'javascript') !== false) {
            $this->attributes->addAttribute('href', $href);
        } elseif (strpos($href, '#') !== false) {
            $this->attributes->addAttribute('href', $href);
        } elseif (function_exists('base_url')) {
            $this->attributes->addAttribute('href', base_url($href));
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Link::getAttributeHref
     *
     * @return string
     */
    public function getAttributeHref()
    {
        return $this->attributes->offsetGet('href');
    }

    // ------------------------------------------------------------------------

    /**
     * Link::setIcon
     *
     * @param \O2System\Framework\Libraries\Ui\Contents\Icon $icon
     *
     * @return static
     */
    public function setIcon(Icon $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Link::active
     *
     * @return static
     */
    public function active()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Link::disabled
     *
     * @return static
     */
    public function disabled()
    {
        $this->attributes->addAttributeClass('disabled');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Link::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        if ($this->hasIcon()) {
            $output[] = $this->icon;
        }

        if ($this->hasTextContent()) {
            $output[] = PHP_EOL . implode('', $this->textContent->getArrayCopy()) . PHP_EOL;
        }

        if ($this->hasChildNodes()) {
            if ( ! $this->hasTextContent()) {
                $output[] = PHP_EOL;
            }

            foreach ($this->childNodes as $childNode) {
                $output[] = $childNode . PHP_EOL;
            }
        }

        $output[] = $this->close();

        return implode('', $output);
    }

    // ------------------------------------------------------------------------

    /**
     * Link::hasIcon
     *
     * @return bool
     */
    public function hasIcon()
    {
        return empty($this->icon) ? false : true;
    }
}