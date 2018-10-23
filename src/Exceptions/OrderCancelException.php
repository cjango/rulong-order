<?php

namespace RuLong\Order\Exceptions;

use RuntimeException;

class OrderCancelException extends RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
