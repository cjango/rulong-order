<?php

namespace RuLong\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuLong\Order\Events\OrderCanceled;
use RuLong\Order\Events\OrderPaid;
use RuLong\Order\Exceptions\OrderException;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

trait OrderHasActions
{

    /**
     * 取消订单
     * @param  integer $channel 订单取消渠道
     */
    public function cancel($channel)
    {
        // 判断订单是否可取消状态
        if (!in_array($this->state, [Order::ORDER_INIT, Order::ORDER_PAID, Order::ORDER_AUDITED])) {
            throw new OrderException("当前订单状态不可取消");
        }

        if (!in_array($this->getOrderStatus('status'), [0, 1])) {
            throw new OrderException("当前订单状态不可取消");
        }

        $this->setOrderStatus('status', $channel);
        $this->state = Order::ORDER_CANCEL;
        $this->save();

        event(new OrderCanceled($this));

        return true;
    }

    /**
     * 订单支付
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:16:02+0800
     * @return [type] [description]
     */
    public function paid()
    {
        // 判断订单是否可取消状态
        if ($this->state != Order::ORDER_INIT) {
            throw new OrderException("订单状态不可支付");
        }

        if ($this->getOrderStatus('pay') != 0) {
            throw new OrderException("订单状态不可支付");
        }

        $this->setOrderStatus('status', 1);
        $this->setOrderStatus('pay', 1);
        $this->state   = Order::ORDER_PAID;
        $this->paid_at = Carbon::now();
        $this->save();

        event(new OrderPaid($this));

        return true;
    }

    /**
     * 标记发货处理中
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:27:34+0800
     * @return [type] [description]
     */
    public function delivering()
    {
        // 判断订单是否可取消状态
        if (in_array($this->state, [Order::ORDER_INIT, Order::ORDER_PAID, Order::ORDER_UNPAID, Order::ORDER_AUDITED])) {
            $this->setOrderStatus('status', 1);
            $this->state = Order::ORDER_DELIVER;
            $this->save();
        } else {
            throw new OrderException('订单非发货状态');
        }
    }

    /**
     * 订单发货
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:16:07+0800
     * @param [type] $express [description]
     * @return [type] [description]
     */
    public function deliver($company = null, $number = null)
    {
        if ($this->express) {
            $this->express->update([
                'company'    => $company,
                'number'     => $number,
                'deliver_at' => Carbon::now(),
            ]);
            $this->setOrderStatus('deliver', 2);
        } else {
            $this->setOrderStatus('deliver', 1);
        }

        $this->state = Order::ORDER_DELIVERED;
        $this->save();

        return true;
    }

    /**
     * 签收订单
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:47:06+0800
     * @return [type] [description]
     */
    public function signin()
    {
        if ($this->express) {
            $this->express->update([
                'receive_at' => Carbon::now(),
            ]);
        }

        $this->setOrderStatus('deliver', 5);
        $this->state = Order::ORDER_SIGNED;
        $this->save();

        return true;
    }

    /**
     * 延迟收货
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:09:15+0800
     * @return [type] [description]
     */
    public function delay()
    {
        $this->setOrderStatus('deliver', 4);
        $this->save();
    }

    /**
     * 未收到
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:11:42+0800
     * @return [type] [description]
     */
    public function unreceived()
    {
        $this->setOrderStatus('deliver', 3);
        $this->save();
    }

    public function completed()
    {
        $this->setOrderStatus('status', 8);
        $this->state = Order::ORDER_COMPLETED;
        $this->save();
    }

    /**
     * 关闭订单
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:14:34+0800
     * @return [type] [description]
     */
    public function close()
    {
        $this->setOrderStatus('status', 9);
        $this->state = Order::ORDER_CLOSED;
        $this->save();
    }

    /**
     * 申请退款
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:57:32+0800
     * @param array $orderDetails [description]
     * @param float $total [description]
     * @param integer $pay [description]
     * @param integer $deliver [description]
     * @return [type] [description]
     */
    public function refunding(array $details, float $total, $pay = 2, $deliver = 7)
    {
        $refund = null;
        DB::transaction(function () use ($details, $total, $pay, $deliver, &$refund) {
            $this->setOrderStatus('pay', $pay);
            $this->setOrderStatus('deliver', $deliver);
            $this->state = Order::REFUND_APPLY;
            $this->save();

            $refund = $this->refund()->create([
                'refund_total' => $total,
                'actual_total' => 0,
                'state'        => Refund::REFUND_APPLY,
            ]);

            foreach ($details as $detail) {
                $refund->items()->save($detail);
            }
        });
        return $refund;
    }
}
