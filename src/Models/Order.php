<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RuLong\Order\Traits\OrderCando;
use RuLong\Order\Traits\OrderHasActions;
use RuLong\Order\Traits\OrderHasAttributes;
use RuLong\Order\Utils\Helper;

class Order extends Model
{
    use OrderCando, OrderHasActions, OrderHasAttributes, SoftDeletes;

    const ORDER_INIT       = 'INIT'; // 订单初始化
    const ORDER_UNPAID     = 'UNPAID'; // 待支付
    const ORDER_PAID       = 'PAID'; // 已支付
    const ORDER_DELIVER    = 'DELIVER'; // 发货处理中
    const ORDER_DELIVERED  = 'DELIVERED'; // 已发货
    const ORDER_SIGNED     = 'SIGNED'; // 已签收
    const REFUND_APPLY     = 'REFUND_APPLY'; // 申请退款
    const REFUND_AGREE     = 'REFUND_AGREE'; // 同意退款
    const REFUND_REFUSE    = 'REFUND_REFUSE'; // 拒绝退款
    const REFUND_PROCESS   = 'REFUND_PROCESS'; // 退款中
    const REFUND_COMPLETED = 'REFUND_COMPLETED'; // 退款完成
    const ORDER_COMPLETED  = 'COMPLETED'; // 已完成
    const ORDER_CLOSED     = 'CLOSED'; // 已关闭
    const ORDER_CANCEL     = 'CANCEL'; // 取消

    const CANCEL_USER   = 2; // 买家取消
    const CANCEL_SELLER = 3; // 卖家取消
    const CANCEL_SYSTEM = 4; // 系统取消

    protected $guarded = [];

    protected $dates = [
        'paid_at',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->orderid = Helper::orderid(config('rulong_order.order_orderid.length'), config('rulong_order.order_orderid.prefix'));
        });
    }

    /**
     * 关联所属用户
     * @Author:<C.Jason>
     * @Date:2018-10-19T14:05:42+0800
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(config('rulong_order.user_model'));
    }

    /**
     * 订单详情
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:35:55+0800
     * @return OrderDetail
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * 订单物流
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:36:03+0800
     * @return OrderExpress
     */
    public function express()
    {
        return $this->hasOne(OrderExpress::class);
    }

    /**
     * 订单日志
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:36:11+0800
     * @return OrderLog
     */
    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    /**
     * 退款单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:15:02+0800
     * @return OrderRefund
     */
    public function refund()
    {
        return $this->hasOne(Refund::class)->orderBy('id', 'desc');
    }

    /**
     * 全部退款单
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:26:18+0800
     * @return [type] [description]
     */
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

}
