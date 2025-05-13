<?php

namespace App\Service\Entity;

use App\Entity\Goods;
use App\Entity\Users;
use App\Enum\Status;
use App\Factory\OrdersFactory;
use App\Factory\OrdersGoodsFactory;
use App\Lock\OrderLockFactory;
use App\Message\OrderMessage;
use App\Repository\OrdersGoodsRepository;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderService
{
    public function __construct(
        private readonly MessageBusInterface   $messageBus,
        private readonly OrderLockFactory      $lockFactory,
        private readonly OrdersFactory         $ordersFactory,
        private readonly OrdersGoodsFactory    $ordersGoodsFactory,
        private readonly UsersRepository       $usersRepository,
        private readonly OrdersRepository      $repository,
        private readonly OrdersGoodsRepository $ordersGoodsRepository
    )
    {
    }

    public function create(int $userId, array $goods): ?int
    {
        $user = $this->usersRepository->find($userId);
        if (null === $user) {
            throw new \RuntimeException("Нет такого пользователя, заказ не будет оформлен");
        }

        /** @var LockInterface[] $locks */
        $locks = [];
        $ids = array_column($goods, 'id');
        sort($ids);

        try {
            foreach ($ids as $id) {
                $lock = $this->lockFactory->create($id);
                if (!$lock->acquire()) {
                    throw new \RuntimeException("Товар заблокирован для оформления");
                }
                $locks[] = $lock;
            }

            $checkStock = $this->checkStock($ids, $goods);
            $status = $checkStock['status'] ?? false;

            if ($status === false) {
                throw new \RuntimeException("Товаров не хватает на складе, заказ не будет оформлен");
            }

            $map = $checkStock['map'];

            $orderId = $this->createOrder($user, $goods, $map);

            $this->messageBus->dispatch(new OrderMessage($orderId));

        } finally {
            foreach ($locks as $lock) {
                $lock->release();
            }
        }
        return $orderId;
    }

    public function checkStock(array $ids, array $goods): array
    {
        //достаем фактические остатки из бд
        $orderItems = $this->repository->findOrderItems($ids);

        $goodsMap = [];
        foreach ($orderItems as $orderItem) {
            $goodsMap[$orderItem->getId()] = $orderItem;
        }

        //товары в заказе
        foreach ($goods as $item) {
            /** @var Goods $product */
            $product = $goodsMap[$item['id']] ?? null;

            if (null === $product || $product->getCount() < $item['count']) {
                return [
                    'status' => false,
                    'map' => []
                ];
            }
        }
        return [
            'status' => true,
            'map' => $goodsMap
        ];
    }

    public function createOrder(Users $user, array $goods, array $goodsMap): int
    {
        $order = $this->ordersFactory->create($user, Status::PENDING);

        foreach ($goods as $item) {
            $product = $goodsMap[$item['id']];
            /** @var Goods $product */
            $ordersGoods = $this->ordersGoodsFactory->create($order, $product, $item['count']);
            $order->addOrdersGood($ordersGoods);
            $this->ordersGoodsRepository->save($ordersGoods);
        }
        $this->repository->save($order);

        return $order->getId();
    }
}