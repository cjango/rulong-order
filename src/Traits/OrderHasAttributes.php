<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;

trait OrderHasAttributes
{

    /**
     * 设置订单分状态
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:40:44+0800
     * @param [type] $type [description]
     * @param [type] $change [description]
     */
    public function setOrderStatus($type, $change)
    {
        $status = sprintf('%04d', $this->status);

        switch ($type) {
            case 'status':
                $this->status = substr_replace($status, $change, 0, 1);
                break;
            case 'pay':
                $this->status = substr_replace($status, $change, 1, 1);
                break;
            case 'deliver':
                $this->status = substr_replace($status, $change, 2, 1);
                break;
            case 'comment':
                $this->status = substr_replace($status, $change, 3, 1);
                break;
        }
    }

    /**
     * 获取订单分状态
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:40:52+0800
     * @param [type] $type [description]
     * @param [type] $change [description]
     * @return [type] [description]
     */
    public function getOrderStatus($type)
    {
        $status = sprintf('%04d', $this->status);

        switch ($type) {
            case 'status':
                return substr($status, 0, 1) ?: 0;
                break;
            case 'pay':
                return substr($status, 1, 1) ?: 0;
                break;
            case 'deliver':
                return substr($status, 2, 1) ?: 0;
                break;
            case 'comment':
                return substr($status, 3, 1) ?: 0;
                break;
        }
    }

    /**
     * 获取订单总金额，使用 bcmath 来确保运算精度
     * @Author:<C.Jason>
     * @Date:2018-10-19T11:19:55+0800
     * @return float
     */
    public function getTotalAttribute(): string
    {
        return bcadd($this->amount, $this->freight, 3);
    }

    /**
     * 获取订单详细状态
     * 订单状态;金钱状态;发货状态;评论状态，
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:57:27+0800
     * @return string
     */
    protected function getStatusTextAttribute(): string
    {
        $status  = $this->getOrderStatus('status');
        $pay     = $this->getOrderStatus('pay');
        $deliver = $this->getOrderStatus('deliver');
        $comment = $this->getOrderStatus('comment');

        $statusStatus = [
            0 => '初始化;',
            1 => '进行中;',
            2 => '买家取消;',
            3 => '卖家取消;',
            4 => '系统取消;',
            8 => '已关闭;',
            9 => '已完成;',
        ];

        $payStatus = [
            0 => '未支付;',
            1 => '已支付;',
            2 => '仅退货款;',
            3 => '部分退款;',
            4 => '全额退款;',
            5 => '拒绝退款;',
            6 => '退款中;',
            7 => '退款完成;',
        ];

        $deliverStatus = [
            0 => '未发货;',
            1 => '无需发货;',
            2 => '已发货;',
            3 => '延迟收货;',
            4 => '未收到;',
            5 => '已签收;',
            6 => '无需退货;',
            7 => '退货中;',
            8 => '收到退货;',
            9 => '未收到退货;',
        ];

        $commentStatus = [
            0 => '未评价;',
            1 => '买家已评;',
            2 => '卖家已评;',
            3 => '双方互评;',
        ];

        $statusText  = $statusStatus[$status] ?? $statusStatus[0];
        $payText     = $payStatus[$pay] ?? $payStatus[0];
        $deliverText = $deliverStatus[$deliver] ?? $deliverStatus[0];
        $commentText = $commentStatus[$comment] ?? $commentStatus[0];

        return $statusText . $payText . $deliverText . $commentText;
    }

    /**
     * 获取订单状态 $this->state_text
     * @Author:<C.Jason>
     * @Date:2018-10-19T10:56:24+0800
     * @return string
     */
    protected function getStateTextAttribute(): string
    {
        switch ($this->state) {
            case Order::ORDER_INIT:
                $state = '订单初始化';
                break;
            case Order::ORDER_UNPAID:
                $state = '待支付';
                break;
            case Order::ORDER_PAID:
                $state = '已支付';
                break;
            case Order::ORDER_DELIVER:
                $state = '发货处理中';
                break;
            case Order::ORDER_DELIVERED:
                $state = '已发货';
                break;
            case Order::ORDER_SIGNED:
                $state = '已签收';
                break;
            case Order::ORDER_COMPLETED:
                $state = '已完成';
                break;
            case Order::ORDER_CLOSED:
                $state = '已关闭';
                break;
            case Order::ORDER_CANCEL:
                $state = '已取消';
                break;
            case Order::REFUND_APPLY:
                $state = '申请退款';
                break;
            case Order::REFUND_AGREE:
                $state = '同意退款';
                break;
            case Order::REFUND_PROCESS:
                $state = '退款中';
                break;
            case Order::REFUND_COMPLETED:
                $state = '退款完毕';
                break;
            case Order::REFUND_REFUSE:
                $state = '拒绝退款';
                break;
            default:
                $state = '未知状态';
                break;
        }

        return $state;
    }

}
