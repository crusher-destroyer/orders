<?php

namespace App\DTO\Response;

use App\Enum\Status;
use OpenApi\Attributes as OA;
use Symfony\Component\PropertyInfo\Type;

class CreateOrderResponse
{
    #[OA\Schema(
        properties: [
            new OA\Property(property: 'status', type: Type::BUILTIN_TYPE_STRING, example: Status::PENDING),
            new OA\Property(property: 'orderId', type: Type::BUILTIN_TYPE_INT),
        ],
        type: Type::BUILTIN_TYPE_OBJECT
    )]
    public function __construct(
        public string $status,
        public ?int $orderId = null
    ) {
    }

}