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


namespace O2System\Framework\Services;


use O2System\Email\DataStructures\Config;
use O2System\Email\Message;
use O2System\Email\Spool;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Email
 * @package O2System\Framework\Services
 */
class Email extends Message
{
    use ConfigCollectorTrait;
    use ErrorCollectorTrait;

    /**
     * Email::__construct
     */
    public function __construct()
    {
        if ($config = config()->loadFile('email', true)) {
            $this->setConfig($config->getArrayCopy());
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Email::subject
     *
     * @param string $subject
     *
     * @return Message
     */
    public function subject($subject)
    {
        $subject = language()->getLine($subject);

        return parent::subject($subject);
    }

    // ------------------------------------------------------------------------

    /**
     * Email::with
     *
     * @param mixed $vars
     * @param mixed $value
     *
     * @return static
     */
    public function with($vars, $value = null)
    {
        view()->with($vars, $value);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::template
     *
     * @param string $filename
     * @param array $vars
     *
     * @return static
     */
    public function template($filename, array $vars = [])
    {
        if ($view = view()->load($filename, $vars, true)) {
            $this->body($view);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::send
     *
     * @return bool
     */
    public function send()
    {
        $spool = new Spool(new Config($this->config));

        if ($spool->send($this)) {
            return true;
        }

        $this->setErrors($spool->getErrors());

        return false;
    }
}