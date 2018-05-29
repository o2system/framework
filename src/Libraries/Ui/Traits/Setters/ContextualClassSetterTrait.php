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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

/**
 * Trait ContextualClassSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait ContextualClassSetterTrait
{
    protected $contextualClassPrefix = null;

    // ------------------------------------------------------------------------

    public function setContextualClass($class)
    {
        if (method_exists($this, $method = 'context' . ucfirst($class))) {
            $this->{$method}();
        }
    }

    // ------------------------------------------------------------------------

    public function contextOutline()
    {
        $this->attributes->replaceAttributeClass($this->contextualClassPrefix . '-',
            $this->contextualClassPrefix . '-outline-');

        $this->setContextualClassPrefix($this->contextualClassPrefix . '-outline');

        return $this;
    }

    public function setContextualClassPrefix($prefix)
    {
        $this->contextualClassPrefix = $prefix;

        return $this;
    }

    public function contextDefault()
    {
        $this->setContextualClassSuffix('default');

        return $this;
    }

    public function setContextualClassSuffix($suffix)
    {
        $this->attributes->removeAttributeClass([
            $this->contextualClassPrefix . '-' . 'default',
            $this->contextualClassPrefix . '-' . 'primary',
            $this->contextualClassPrefix . '-' . 'secondary',
            $this->contextualClassPrefix . '-' . 'success',
            $this->contextualClassPrefix . '-' . 'info',
            $this->contextualClassPrefix . '-' . 'warning',
            $this->contextualClassPrefix . '-' . 'danger',
            $this->contextualClassPrefix . '-' . 'neutral',
        ]);
        $this->attributes->addAttributeClass($this->contextualClassPrefix . '-' . $suffix);

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextPrimary()
    {
        $this->setContextualClassSuffix('primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextSecondary()
    {
        $this->setContextualClassSuffix('secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextSuccess()
    {
        $this->setContextualClassSuffix('success');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextInfo()
    {
        $this->setContextualClassSuffix('info');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextWarning()
    {
        $this->setContextualClassSuffix('warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextDanger()
    {
        $this->setContextualClassSuffix('danger');

        return $this;
    }

    public function contextLight()
    {
        $this->setContextualClassSuffix('light');

        return $this;
    }

    public function contextDark()
    {
        $this->setContextualClassSuffix('dark');

        return $this;
    }

    public function contextLink()
    {
        $this->attributes->addAttributeClass($this->contextualClassPrefix . '-link');

        return $this;
    }
}