<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;

class RefundExpress extends Model
{

    protected $table = 'order_refund_expresses';

    protected $guarded = [];

    protected $dates = [
        'deliver_at',
        'receive_at',
    ];

    /**
     * 所属退款单
     * @Author:<C.Jason>
     * @Date:2018-10-23T10:56:16+0800
     * @return Refund
     */
    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

}
