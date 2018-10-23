<?php

namespace RuLong\Order\Facades;

use Illuminate\Support\Facades\Facade;

class Refunds extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RuLong\Order\Refunds::class;
    }
}
