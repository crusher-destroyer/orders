<?php

namespace App\MessageHandler;

use App\Entity\Goods;
use App\Enum\Status;
use App\Message\OrderMessage;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class OrderHandler
{
    public function __construct(
        private OrdersRepository $repository,
        private EntityManagerInterface $em
    )
    {
    }

    public function __invoke(OrderMessage $message): void
    {
        $order = $this->repository->find($message->orderId);

        if (!$order || $order->getStatus() !== Status::PENDING) {
            return;
        }

        try {
            $this->em->wrapInTransaction(function () use ($order) {
                foreach ($order->getOrdersGoods() as $orderItem) {
                    $this->decrementStock($orderItem->getGoods(), $orderItem->getCount());
                }

                $order->setStatus(Status::COMPLETED);
                $this->em->flush();
            });

        } catch (\Throwable $e) {
            $order->setStatus(Status::CANCELLED);
            $this->em->flush();
        }
    }

    private function decrementStock(Goods $product, int $countOrdered): void
    {
        if ($product->getCount() < $countOrdered) {
            throw new \RuntimeException("Товаров не хватает на складе, заказ не будет оформлен");
        }

        $product->decreaseCount($countOrdered);
        $this->em->persist($product);
    }
}