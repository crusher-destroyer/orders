<?php

namespace App\Message;

readonly class OrderMessage
{
    public function __construct(
        public int $userId,
        public array $goods,
    ) {
    }
}