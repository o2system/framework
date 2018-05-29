<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 05/04/18
 * Time: 08.03
 */

namespace O2System\Framework\Libraries;


use O2System\Email\Datastructures\Config;
use O2System\Email\Message;
use O2System\Email\Spool;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

class Email extends Message
{
    use ConfigCollectorTrait;

    public function __construct()
    {
        if ($config = config()->loadFile('email', true)) {
            $this->setConfig($config->getArrayCopy());
        }
    }

    public function subject($subject)
    {
        $subject = language()->getLine($subject);

        return parent::subject($subject);
    }

    public function with($vars, $value = null)
    {
        view()->with($vars, $value);

        return $this;
    }

    public function template($filename, array $vars = [])
    {
        if ($view = view()->load($filename, $vars, true)) {
            $this->body($view);
        }

        return $this;
    }

    public function send()
    {
        $spool = new Spool(new Config($this->config));

        return (bool)$spool->send($this);
    }
}