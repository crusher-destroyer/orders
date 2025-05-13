<?php

namespace App\Service\Entity;

use App\Entity\Goods;
use App\Factory\OrdersFactory;
use App\Factory\OrdersGoodsFactory;
use App\Message\OrderMessage;
use App\Repository\OrdersGoodsRepository;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderService
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly OrdersFactory $ordersFactory,
        private readonly OrdersGoodsFactory $ordersGoodsFactory,
        private readonly UsersRepository $usersRepository,
        private readonly OrdersRepository $repository,
        private readonly OrdersGoodsRepository $ordersGoodsRepository
    )
    {
    }

    public function sendMessage(OrderMessage $orderMessage): void
    {
        $this->messageBus->dispatch($orderMessage);
    }

    /**
     * @throws \Exception
     */
    public function process(OrderMessage $message, array $ids): void
    {
        $user = $this->usersRepository->find($message->userId);
        if (null === $user) {
            throw new \RuntimeException("Нет такого пользователя, заказ не будет оформлен");
        }

        $order = $this->ordersFactory->create($user);

        //достаем фактические остатки в бд
        $orderItems = $this->repository->findOrderItems($ids);

        $goodsMap = [];
        foreach ($orderItems as $orderItem) {
            $goodsMap[$orderItem->getId()] = $orderItem;
        }

        //товары в заказе
        foreach ($message->goods as $item) {
            /** @var Goods $product */
            $product = $goodsMap[$item['id']] ?? null;

            if (null === $product || $product->getCount() < $item['count']) {
                throw new \RuntimeException("Товаров не хватает на складе, заказ не будет оформлен");
            }
        }

        foreach ($message->goods as $item) {
            $product = $goodsMap[$item['id']];
            /** @var Goods $product */
            $product->decreaseCount($item['count']);
            $ordersGoods = $this->ordersGoodsFactory->create($order, $product, $item['count']);
            $order->addOrdersGood($ordersGoods);
            $this->ordersGoodsRepository->save($ordersGoods);
        }

        $this->repository->save($order);
    }
}