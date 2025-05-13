<?php

namespace App\Lock;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class OrderLockFactory
{
    public const KEY = 'order_';

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly float       $ttl
    ) {
    }

    public function create(int $id): LockInterface
    {
        $key = self::KEY . hash('sha256', $id);
        return $this->lockFactory->createLock($key, $this->ttl);
    }
}