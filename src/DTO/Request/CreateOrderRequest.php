<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

final readonly class CreateOrderRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(example: 123)]
        public int $userId,

        /**
         * @var array<string>
         */
        #[Assert\NotNull(message: 'goods не может быть null')]
        #[Assert\Type('array', message: 'goods должен быть массивом')]
        #[Assert\NotBlank(message: 'goods не может быть пуст')]
        #[Assert\All([
            new Assert\Collection([
                'fields' => [
                    'id' => [
                        new Assert\NotBlank(),
                        new Assert\Type('int'),
                        new Assert\Positive(),
                    ],
                    'count' => [
                        new Assert\NotBlank(),
                        new Assert\Type('int'),
                        new Assert\GreaterThanOrEqual(1, message: "Товаров в заказе должно быть больше 0"),
                    ],
                ],
                'allowExtraFields' => false,
                'allowMissingFields' => false,
            ])
        ])]
        public array $goods
    ) {}
}