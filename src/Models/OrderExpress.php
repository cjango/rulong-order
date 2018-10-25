<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderExpress extends Model
{
    protected $guarded = [];

    protected $dates = [
        'deliver_at',
        'receive_at',
    ];

    /**
     * 所属订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:49:06+0800
     * @return Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 设置收货地址详细内容
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:10:02+0800
     * @param Addressbook $Addressbook
     */
    public function setInstanceAttribute($Addressbook)
    {
        $this->attributes['name']    = $Addressbook->getName();
        $this->attributes['mobile']  = $Addressbook->getMobile();
        $this->attributes['address'] = $Addressbook->getAddress();
    }
}
