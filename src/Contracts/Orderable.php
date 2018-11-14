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
    function getTitle();

    /**
     * 获取商品单价
     * @return string
     */
    function getPrice();

    /**
     * 获取商品积分
     * @return string
     */
    function getScore();

    /**
     * 获取商品库存
     * @return string
     */
    function getStock();

    /**
     * 扣除库存方法
     */
    function deductStock($stock);

    /**
     * 增加库存方法
     */
    function addStock($stock);

}
