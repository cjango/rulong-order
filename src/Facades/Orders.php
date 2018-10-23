<?php

namespace RuLong\Order\Facades;

use Illuminate\Support\Facades\Facade;

class Orders extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RuLong\Order\Orders::class;
    }
}
