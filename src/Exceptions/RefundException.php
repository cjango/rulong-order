<?php

namespace RuLong\Order\Exceptions;

use RuntimeException;

class RefundException extends RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
