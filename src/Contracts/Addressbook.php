<?php

namespace RuLong\Order\Contracts;

/**
 * 可购买商品 契约
 */
interface Addressbook
{
    /**
     * 收件人姓名
     * @return string
     */
    public function getName();

    /**
     * 收件人电话
     * @return string
     */
    public function getMobile();

    /**
     * 收件人详细地址
     * @return string
     */
    public function getAddress();

}
