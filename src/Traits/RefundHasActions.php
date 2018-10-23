<?php

namespace RuLong\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuLong\Order\Events\RefundAgreed;
use RuLong\Order\Events\RefundCompleted;
use RuLong\Order\Events\RefundProcessed;
use RuLong\Order\Events\RefundRefused;
use RuLong\Order\Exceptions\RefundException;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

trait RefundHasActions
{

    /**
     * 同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:39:04+0800
     * @return RefundException|boolean
     */
    public function agree()
    {
        try {
            throw new RefundException('当前订单状态无法同意退款');

            DB::transaction(function () {
                $this->state        = Refund::REFUND_AGREE;
                $this->actual_total = $this->refund_total;
                $this->save();

                $this->order->state = Order::REFUND_AGREE;
                $this->order->save();

                event(new RefundAgreed($this));
            });

            return true;

        } catch (\Exception $e) {

        }
    }

    /**
     * 拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:07+0800
     * @param string|null $remark 拒绝原因
     * @return RefundException|boolean
     */
    public function refuse(string $remark = null)
    {
        DB::transaction(function () use ($remark) {
            $this->state  = Refund::REFUND_REFUSE;
            $this->remark = $remark;
            $this->save();

            $this->order->state = Order::REFUND_REFUSE;
            $this->order->save();

            event(new RefundRefused($this));
        });

        return true;
    }

    /**
     * 标记退款中
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:29+0800
     * @return RefundException|boolean
     */
    public function process()
    {
        DB::transaction(function () {
            $this->state       = Refund::REFUND_PROCESS;
            $this->refunded_at = Carbon::now();
            $this->save();

            $this->order->state = Order::REFUND_PROCESS;
            $this->order->save();

            event(new RefundProcessed($this));
        });

        return true;
    }

    /**
     * 标记退款完成
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:36+0800
     * @return RefundException|boolean
     */
    public function complete()
    {
        DB::transaction(function () {
            $this->state = Refund::REFUND_COMPLETED;
            $this->save();

            $this->order->state = Order::REFUND_COMPLETED;
            $this->order->save();

            event(new RefundCompleted($this));
        });

        return true;
    }

}
