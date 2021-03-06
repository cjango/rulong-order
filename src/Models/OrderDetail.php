<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;
use RuLong\Order\Contracts\Orderable;
use RuLong\Order\Exceptions\OrderException;

class OrderDetail extends Model
{

    const UPDATED_AT = null;

    protected $guarded = [];

    public $goodsCanBuy;

    public $goodsTitle;

    /**
     * 设置订单详情
     * @Author:<C.Jason>
     * @Date:2018-10-25T14:30:50+0800
     * @param Orderable $goods 商品实例
     */
    public function setGoodsAttribute($goods)
    {
        if (!($goods instanceof Orderable)) {
            throw new OrderException('购买的商品必须实现 Orderable 接口');
        }
        $this->goodsCanBuy = $goods->getStock();
        $this->goodsTitle  = $goods->getTitle();

        $this->attributes['price']     = $goods->getPrice();
        $this->attributes['score']     = $goods->getScore();
        $this->attributes['item_id']   = $goods->id;
        $this->attributes['item_type'] = get_class($goods);
    }

    /**
     * 所属订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:48:40+0800
     * @return Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 所属商品
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:48:51+0800
     * @return
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * 获取最大可退数量
     * @Author:<C.Jason>
     * @Date:2018-10-23T11:36:43+0800
     * @return integer
     */
    public function getMaxRefundAttribute(): int
    {
        $refundNumbers = $this->refundItems()->whereHas('refund', function ($query) {
            $query->whereNotIn('state', [Order::REFUND_REFUSE]);
        })->sum('number');

        return $this->number - $refundNumbers;
    }

    /**
     * 判断订单条目，是否可退款
     * @Author:<C.Jason>
     * @Date:2018-10-26T10:43:08+0800
     * @return [type] [description]
     */
    public function canRefund()
    {
        return $this->max_refund > 0;
    }

    /**
     * 关联退款详单
     * @Author:<C.Jason>
     * @Date:2018-10-23T11:44:22+0800
     * @return RefundItem
     */
    public function refundItems()
    {
        return $this->hasMany(RefundItem::class);
    }

    /**
     * 获取单个商品总价
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:51:19+0800
     * @return string
     */
    public function getTotalAttribute(): string
    {
        return bcmul($this->price, $this->number, 3);
    }
}
