<?php

namespace App\Controller;


use App\DTO\Request\CreateOrderRequest;
use App\Message\OrderMessage;
use App\Service\AppSerializer;
use App\Service\Entity\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrderController extends BaseController
{
    public function __construct(
        AppSerializer $appSerializer,
        private readonly OrderService $orderService
    ) {
        parent::__construct($appSerializer);
    }
    #[Route('/', methods: [Request::METHOD_POST])]
    public function create(Request $request, CreateOrderRequest $dto)
    {
        $userId = $request->request->get('product_id');
        $goods = $request->request->get('goods');

        return $this->appJson($this->orderService->sendMessage(new OrderMessage($dto->userId, $dto->goods)));

    }

}