<?php

namespace RuLong\Order\Utils;

class Helper
{

    /**
     * 创建一个订单号
     * @Author:<C.Jason>
     * @Date:2018-10-23T13:50:33+0800
     * @param integer $length 订单号长度
     * @param string $prefix  订单号前缀
     * @return string
     */
    public static function orderid($length = 20, $prefix = '')
    {
        if ($length > 30) {
            $length = 30;
        }
        $fixed = $length - 12;
        if (strlen($prefix) >= $fixed) {
            $prefix = substr($prefix, 0, $fixed);
        }

        $code = date('ymdHis') . sprintf("%0" . $fixed . "d", mt_rand(0, pow(10, $fixed) - 1));
        if (!empty($prefix)) {
            $code = $prefix . substr($code, 0, $length - strlen($prefix));
        }
        return $code;
    }
}
