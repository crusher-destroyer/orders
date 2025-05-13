<?php

namespace App\Factory;

use App\Entity\Goods;
use App\Entity\Orders;
use App\Entity\OrdersGoods;

class OrdersGoodsFactory
{
    public function create(Orders $order, Goods $item, int $count): OrdersGoods
    {
        $ordersGoods = new OrdersGoods();
        $ordersGoods->setOrders($order)
            ->setGoods($item)
            ->setCount($count)
            ->setLockedPrice($item->getPrice());
        return $ordersGoods;
    }

}