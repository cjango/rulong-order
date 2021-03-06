<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RuLong\Order\Traits\RefundCando;
use RuLong\Order\Traits\RefundHasActions;
use RuLong\Order\Utils\Helper;

class Refund extends Model
{

    use RefundHasActions, RefundCando, SoftDeletes;

    const REFUND_APPLY     = 'REFUND_APPLY'; // 申请退款
    const REFUND_AGREE     = 'REFUND_AGREE'; // 同意退款
    const REFUND_REFUSE    = 'REFUND_REFUSE'; // 拒绝退款
    const REFUND_PROCESS   = 'REFUND_PROCESS'; // 退款中
    const REFUND_COMPLETED = 'REFUND_COMPLETED'; // 退款完成

    protected $table = 'order_refunds';

    protected $guarded = [];

    protected $dates = [
        'refunded_at',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->orderid = Helper::orderid(config('rulong_order.refund_orderid.length'), config('rulong_order.refund_orderid.prefix'));
        });
    }

    /**
     * 所属订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:45:04+0800
     * @return Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 退款单详情
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:45:26+0800
     * @return OrderRefundItem
     */
    public function items()
    {
        return $this->hasMany(RefundItem::class);
    }

    /**
     * 退款单物流
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:36:03+0800
     * @return RefundExpress
     */
    public function express()
    {
        return $this->hasOne(RefundExpress::class);
    }

    /**
     * 获取退款状态 $this->state_text
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:56:24+0800
     * @return string
     */
    protected function getStateTextAttribute(): string
    {
        switch ($this->state) {
            case self::REFUND_APPLY:
                $state = '退款申请中';
                break;
            case self::REFUND_AGREE:
                $state = '同意退款';
                break;
            case self::REFUND_PROCESS:
                $state = '退款中';
                break;
            case self::REFUND_COMPLETED:
                $state = '退款完毕';
                break;
            case self::REFUND_REFUSE:
                $state = '拒绝退款';
                break;
            default:
                $state = '未知状态';
                break;
        }

        return $state;
    }
}
