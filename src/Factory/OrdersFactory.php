<?php

namespace App\Factory;

use App\Entity\Orders;
use App\Entity\Users;

class OrdersFactory
{
    public function create(Users $user): Orders
    {
        $order = new Orders();
        $order->setCustomer($user);
        $order->setStatus('pending');
        return $order;
    }

}