<?php

namespace RuLong\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

trait RefundHasActions
{

    /**
     * 同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:39:04+0800
     * @return [type] [description]
     */
    public function agree()
    {
        DB::transaction(function () {
            $this->state        = Refund::REFUND_AGREE;
            $this->actual_total = $this->refund_total;
            $this->save();

            $this->order->state = Order::REFUND_AGREE;
            $this->order->save();
        });

        return true;
    }

    /**
     * 拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:07+0800
     * @param string|null $remark 拒绝原因
     * @return
     */
    public function refuse(string $remark = null)
    {
        DB::transaction(function () use ($remark) {
            $this->state  = Refund::REFUND_REFUSE;
            $this->remark = $remark;
            $this->save();

            $this->order->state = Order::REFUND_REFUSE;
            $this->order->save();
        });

        return true;
    }

    /**
     * 标记退款中
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:29+0800
     * @return [type] [description]
     */
    public function process()
    {
        DB::transaction(function () {
            $this->state       = Refund::REFUND_PROCESS;
            $this->refunded_at = Carbon::now();
            $this->save();

            $this->order->state = Order::REFUND_PROCESS;
            $this->order->save();
        });

        return true;
    }

    /**
     * 标记退款完成
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:36+0800
     * @return [type] [description]
     */
    public function completed()
    {
        DB::transaction(function () {
            $this->state = Refund::REFUND_COMPLETED;
            $this->save();

            $this->order->state = Order::REFUND_COMPLETED;
            $this->order->save();
        });

        return true;
    }

}
