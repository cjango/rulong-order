<?php

namespace RuLong\Order\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{

    const UPDATED_AT = null;

    protected $guarded = [];

    public function setUserAttribute($user)
    {
        if (!is_null($user)) {
            $this->attributes['user_id']   = $user->id ?? 0;
            $this->attributes['user_type'] = get_class($user) ?? null;
        }
    }

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
     * 操作用户
     * @Author:<C.Jason>
     * @Date:2018-10-26T11:42:37+0800
     * @return
     */
    public function user()
    {
        return $this->morphTo();
    }
}
