<?php

namespace App\DTO\Request;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateOrderRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Поле userId не может быть пустым')]
        #[Assert\Positive]
        #[Assert\Type(
            type: Type::BUILTIN_TYPE_INT,
            message: 'Поле userId должно быть числом'
        )]
        public int $userId,

        /**
         * @var OrderItemRequest[]
         */
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type(type: OrderItemRequest::class),
        ])]
        #[Assert\Valid]
        public array $goods
    ) {
    }
}