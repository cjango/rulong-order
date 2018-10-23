<?php

namespace RuLong\Order\Exceptions;

use RuntimeException;

class OrderException extends RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
