<?php

namespace RuLong\Order\Contracts;

/**
 * 可购买商品 契约
 */
interface Orderable
{

    /**
     * 获取商品名称
     * @return string
     */
    public function getTitle();

    /**
     * 获取商品单价
     * @return string
     */
    public function getPrice();

    /**
     * 获取商品库存
     * @return string
     */
    public function getStock();

    /**
     * 扣除库存方法
     */
    public function deductStock($stock);

    /**
     * 增加库存方法
     */
    public function addStock($stock);

}
