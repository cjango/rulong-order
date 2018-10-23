<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{

    const UPDATED_AT = null;

    protected $table = 'order_refund_items';

    protected $guarded = [];

    /**
     * 所属退款单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:44:57+0800
     * @return [type] [description]
     */
    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    /**
     * 所属订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:45:59+0800
     * @return Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 所属订单详情
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:46:04+0800
     * @return OrderDetail
     */
    public function detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    /**
     * 商品详情
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:46:20+0800
     * @return
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * 获取单个商品总价
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:51:19+0800
     * @return string
     */
    public function getItemTotalAttribute(): string
    {
        return bcmul($this->price, $this->number, 3);
    }

    public function setDetailAttribute($detail)
    {
        $this->attributes['order_id']        = $detail->order_id;
        $this->attributes['order_detail_id'] = $detail->id;
        $this->attributes['item_id']         = $detail->item_id;
        $this->attributes['item_type']       = $detail->item_type;
        $this->attributes['price']           = $detail->price;
    }
}
