<?php

namespace App\Controller;

use App\Service\AppSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private readonly AppSerializer $appSerializer
    ) {
    }

    public function appJson(
        mixed $data,
        int   $status = Response::HTTP_OK,
        array $headers = [],
        array $context = [],
    ): Response {
        return new Response(
            $this->appSerializer->serialize($data, 'json', $context),
            $status,
            array_merge($headers, [
                'content-type' => 'application/json',
            ]),
        );
    }
}