<?php

namespace RuLong\Order;

use Illuminate\Support\Facades\DB;
use RuLong\Order\Contracts\Addressbook;
use RuLong\Order\Contracts\Orderable;
use RuLong\Order\Events\OrderCreated;
use RuLong\Order\Exceptions\OrderException;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\OrderDetail;
use RuLong\Order\Models\OrderExpress;
use RuLong\Order\Models\Refund;

class Orders
{

    /**
     * 创建订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T16:20:27+0800
     * @param integer $userID  用户ID
     * @param $items   商品详情
     * array[
     *     new OrderDetail(['goods' => RuLong\Order\Contracts\Orderable, 'number' => int]),
     *     new OrderDetail(['goods' => RuLong\Order\Contracts\Orderable, 'number' => int]),
     * ]
     * @param Addressbook $address 收获地址
     * @param string $remark  订单备注
     * @param float  $amount  总金额，auto的时候自动计算
     * @param float  $freight 运费
     * @return boolean|OrderException
     */
    public function create(int $userID, array $items, Addressbook $address = null, string $remark = null, float $amount = null, float $freight = null)
    {
        try {
            if (is_null($amount) && !empty($items)) {
                $amount = 0;
                $score  = 0;
                foreach ($items as $item) {
                    if ($item->goodsCanBuy < $item->number) {
                        throw new OrderException('【' . $item->goodsTitle . '】商品库存不足');
                    }
                    $amount += $item->price * $item->number;
                    $score += $item->score * $item->number;
                }
            } elseif (is_null($amount) && !is_numeric($amount)) {
                throw new OrderException('订单金额必须是数字类型');
            }

            DB::transaction(function () use ($userID, $items, $address, $amount, $score, $freight, $remark) {
                // 创建订单
                $order = Order::create([
                    'user_id' => $userID,
                    'amount'  => $amount,
                    'score'   => $score,
                    'freight' => $freight,
                    'state'   => Order::ORDER_INIT,
                    'remark'  => $remark,
                ]);
                // 创建订单详情
                $order->details()->saveMany($items);
                // 自动扣除库存
                foreach ($order->details as $detail) {
                    $detail->item->deductStock($detail->number);
                }

                // 保存收获地址，如果收获地址是null，说明不用发货
                if (!is_null($address)) {
                    $express = new OrderExpress(['instance' => $address]);
                    $order->express()->save($express);
                }

                event(new OrderCreated($order));

                if (config('rulong_order.auto_audit') === true) {
                    $order->audit(true);
                }
            });

            return true;
        } catch (\Exception $e) {
            throw new OrderException($e->getMessage());
        }
    }

    /**
     * 订单审核
     * @Author:<C.Jason>
     * @Date:2018-10-25T16:16:06+0800
     * @param Order $order    订单实例
     * @param boolean $result 审核结果
     * @return boolean|OrderException
     */
    public function audit(Order $order, $result = true)
    {
        return $order->audit($result);
    }

    /**
     * 取消订单
     * @param  Order   $order   订单实例
     * @param  integer $channel 取消渠道
     * [CANCEL_USER, CANCEL_SELLER, CANCEL_SYSTEM]
     * @return boolean|OrderCancelException
     */
    public function cancel(Order $order, $channel = Order::CANCEL_SYSTEM)
    {
        return $order->cancel($channel);
    }

    /**
     * 订单支付
     * @return [type] [description]
     */
    public function paid(Order $order)
    {
        return $order->paid();
    }

    /**
     * 标记订单 发货处理中
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:29:56+0800
     * @param Order $order [description]
     * @return [type] [description]
     */
    public function delivering(Order $order)
    {
        $order->delivering();
    }

    /**
     * 订单发货
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:43:38+0800
     * @param Order  $order 订单实例
     * @param string $company 物流公司
     * @param string $number  物流单号
     * @return [type] [description]
     */
    public function deliver(Order $order, $company = null, $number = null)
    {
        return $order->deliver($company, $number);
    }

    /**
     * 签收
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:46:21+0800
     * @param Order $order 订单实例
     * @return [type] [description]
     */
    public function signin(Order $order)
    {
        return $order->signin();
    }

    /**
     * 延迟收货
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:10:04+0800
     * @return [type] [description]
     */
    public function delay(Order $order)
    {
        return $order->delay();
    }

    /**
     * 未收到
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:10:04+0800
     * @return [type] [description]
     */
    public function unreceive(Order $order)
    {
        return $order->unreceive();
    }

    /**
     * 交易完成
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:15:48+0800
     */
    public function complete(Order $order)
    {
        return $order->complete();
    }

    /**
     * 关闭订单
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:13:13+0800
     * @param Order $order [description]
     * @return [type] [description]
     */
    public function close(Order $order)
    {
        return $order->close();
    }

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
    public function refund(Order $order, array $items, float $total = null)
    {
        return \Refunds::create($order, $items, $total);
    }

}
