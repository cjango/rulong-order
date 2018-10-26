<?php

namespace RuLong\Order;

use Illuminate\Support\Facades\DB;
use RuLong\Order\Events\RefundApplied;
use RuLong\Order\Exceptions\OrderException;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\OrderDetail;
use RuLong\Order\Models\Refund;
use RuLong\Order\Models\RefundItem;

class Refunds
{

    /**
     * 申请退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T10:35:42+0800
     * @param Order $order 要退款的订单
     * @param array $items 退款项目
     * [
     *     ['item_id' => integer, 'number' => integer],
     *     ['item_id' => integer, 'number' => integer],
     * ]
     * @param float $total 申请退款金额
     * @return [type] [description]
     */
    public function create(Order $order, array $items, float $total = null)
    {
        try {
            if (!$order->canRefund()) {
                throw new OrderException('订单状态不可退款');
            }

            if (empty($items)) {
                throw new OrderException('至少选择一项退款商品');
            }
            $maxAmount   = 0;
            $refundItems = [];

            //判断最大可退数量
            foreach ($items as $item) {
                $detail = OrderDetail::find($item['item_id']);
                if ($item['number'] <= 0) {
                    throw new OrderException('【' . $detail->item->getTitle() . '】退货数量必须大于0');
                }
                if ($item['number'] > $detail->max_refund) {
                    throw new OrderException('【' . $detail->item->getTitle() . '】超过最大可退数量');
                }
                $maxAmount += $detail->price * $item['number'];
                $refundItems[] = new RefundItem(['detail' => $detail, 'number' => $item['number']]);
            }

            // 自动计算退款金额
            if (is_null($total)) {
                $total = $maxAmount;
            } elseif (!in_array($order->getOrderStatus('deliver'), [0, 1, 4]) && $total > $maxAmount) {
                throw new OrderException('超过最大可退金额');
            }

            DB::transaction(function () use ($order, $total, $refundItems) {
                // 判断退款金额
                if (in_array($order->getOrderStatus('deliver'), [0, 1, 4, 6]) && $order->amount == $total) {
                    $total = $order->total;
                    // 如果是未发货，无需发货，未收到的，直接退全款
                    $order->setOrderStatus('pay', 4);
                } elseif ($order->total == $total) {
                    $order->setOrderStatus('pay', 4);
                } elseif ($order->amount == $total) {
                    $order->setOrderStatus('pay', 2);
                } else {
                    $order->setOrderStatus('pay', 3);
                }

                if (in_array($order->getOrderStatus('deliver'), [0, 1, 8])) {
                    $order->setOrderStatus('deliver', 6);
                }

                $order->state = Order::REFUND_APPLY;
                $order->save();

                $refund = $order->refund()->create([
                    'refund_total' => $total,
                    'state'        => Refund::REFUND_APPLY,
                ]);

                $refund->items()->saveMany($refundItems);

                event(new RefundApplied($order, $refund));
            });

            return true;
        } catch (\Exception $e) {
            throw new OrderException($e->getMessage());
        }
    }

    /**
     * 同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:20:41+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function agree(Refund $refund)
    {
        return $refund->agree();
    }

    /**
     * 拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:18+0800
     * @param Refund $refund 退款单实例
     * @param string|null $remark 拒绝备注
     * @return RefundException|boolean
     */
    public function refuse(Refund $refund, string $remark = null)
    {
        return $refund->refuse($remark);
    }

    /**
     * 退款中
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:21+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function deliver(Refund $refund)
    {
        return $refund->deliver();
    }

    /**
     * 退款完成
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:23+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function complete(Refund $refund)
    {
        return $refund->complete();
    }

}
