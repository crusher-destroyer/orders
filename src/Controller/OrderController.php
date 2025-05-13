<?php

namespace App\Controller;


use App\DTO\Request\CreateOrderRequest;
use App\DTO\Response\CreateOrderResponse;
use App\Service\AppSerializer;
use App\Service\Entity\OrderService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

final class OrderController extends BaseController
{
    public function __construct(
        AppSerializer $appSerializer,
        private readonly OrderService $orderService
    ) {
        parent::__construct($appSerializer);
    }
    #[Route('/', methods: [Request::METHOD_POST])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает id заказа',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(
                    property: 'result',
                    type: Type::BUILTIN_TYPE_ARRAY,
                    items: new OA\Items(ref: new Model(type: CreateOrderResponse::class)),
                ),
            ]
        )
    )]
    #[OA\QueryParameter(name: 'userId', description: 'id пользователя', required: true)]
    #[OA\QueryParameter(name: 'goods', description: 'товары', required: true)]
    public function create(
        #[MapQueryString]
        CreateOrderRequest $request
    ): Response
    {
       return $this->appJson($this->orderService->create($request->userId, $request->goods));
    }
}