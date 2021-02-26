<?php

namespace EnjoyJob\Exception;

use Exception as BaseException;

class Exception extends BaseException
{
    public function __construct($msg = 'unknown err', $code = -1)
    {
        parent::__construct($msg, $code);
    }

    public function __toString()
    {
        return $this->getFile().':'.$this->getLine().'|'.$this->getCode().'|'.$this->getMessage();
    }
}
