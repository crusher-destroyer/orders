<?php

namespace App\MessageHandler;

use App\Factory\OrdersFactory;
use App\Factory\OrdersGoodsFactory;
use App\Lock\OrderLockFactory;
use App\Message\OrderMessage;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use App\Service\Entity\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class OrderHandler
{
    public function __construct(
        private OrderLockFactory       $lockFactory,
        private EntityManagerInterface $em,
        private OrderService           $orderService
    )
    {
    }

    public function __invoke(OrderMessage $message): void
    {
        /** @var LockInterface[] $locks */
        $locks = [];

        try {
            $ids = array_column($message->goods, 'id');
            sort($ids);

            foreach ($ids as $id) {
                $lock = $this->lockFactory->create($id);
                if (!$lock->acquire()) {
                    throw new \RuntimeException("Товар заблокирован для оформления");
                }
                $locks[] = $lock;
            }

            $this->em->wrapInTransaction(function () use ($message, $ids) {
                $this->orderService->process($message, $ids);
            });

        } catch (\Exception $exception) {
            $this->releaseLocks($locks); // если прервали обработку, освобождаем локи
            throw $exception;
        } finally {
            $this->releaseLocks($locks);
        }
    }

    private function releaseLocks(array $locks): void
    {
        foreach ($locks as $lock) {
            if ($lock->isAcquired()) {
                $lock->release();
            }
        }
    }

}