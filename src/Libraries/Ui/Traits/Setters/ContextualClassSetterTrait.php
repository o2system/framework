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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

/**
 * Trait ContextualClassSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait ContextualClassSetterTrait
{
    /**
     * ContextualClassSetterTrait::$contextualClassPrefix
     *
     * @var string|null
     */
    protected $contextualClassPrefix = null;

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::setContextualClass
     *
     * @param string $class
     */
    public function setContextualClass($class)
    {
        if (method_exists($this, $method = 'context' . ucfirst($class))) {
            $this->{$method}();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextOutline
     *
     * @return static
     */
    public function contextOutline()
    {
        $this->attributes->replaceAttributeClass($this->contextualClassPrefix . '-',
            $this->contextualClassPrefix . '-outline-');

        $this->setContextualClassPrefix($this->contextualClassPrefix . '-outline');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::setContextualClassPrefix
     *
     * @param string $prefix
     *
     * @return static
     */
    public function setContextualClassPrefix($prefix)
    {
        $this->contextualClassPrefix = $prefix;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextDefault
     *
     * @return static
     */
    public function contextDefault()
    {
        $this->setContextualClassSuffix('default');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::setContextualClassSuffix
     *
     * @param string $suffix
     *
     * @return static
     */
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

    /**
     * ContextualClassSetterTrait::contextPrimary
     *
     * @return static
     */
    public function contextPrimary()
    {
        $this->setContextualClassSuffix('primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextSecondary
     *
     * @return static
     */
    public function contextSecondary()
    {
        $this->setContextualClassSuffix('secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextSuccess
     *
     * @return static
     */
    public function contextSuccess()
    {
        $this->setContextualClassSuffix('success');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextInfo
     *
     * @return static
     */
    public function contextInfo()
    {
        $this->setContextualClassSuffix('info');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextWarning
     *
     * @return static
     */
    public function contextWarning()
    {
        $this->setContextualClassSuffix('warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextDanger
     *
     * @return static
     */
    public function contextDanger()
    {
        $this->setContextualClassSuffix('danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextLight
     *
     * @return static
     */
    public function contextLight()
    {
        $this->setContextualClassSuffix('light');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextDark
     *
     * @return static
     */
    public function contextDark()
    {
        $this->setContextualClassSuffix('dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ContextualClassSetterTrait::contextLink
     *
     * @return static
     */
    public function contextLink()
    {
        $this->attributes->addAttributeClass($this->contextualClassPrefix . '-link');

        return $this;
    }
}