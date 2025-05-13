<?php

namespace App\DTO\Request;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderItemRequest
{
    public function __construct(
    #[Assert\NotBlank(message: 'Поле id товара не может быть пустым')]
    #[Assert\Type(
        type: Type::BUILTIN_TYPE_INT,
        message: 'Поле id товара должно быть числом'
    )]
    #[Assert\Positive]
    public int $id,

    #[Assert\NotBlank(message: 'Поле count не может быть пустым')]
    #[Assert\Type(
        type: Type::BUILTIN_TYPE_INT,
        message: 'Поле count должно быть числом'
    )]
    #[Assert\Positive]
    public int $count
) {}

}