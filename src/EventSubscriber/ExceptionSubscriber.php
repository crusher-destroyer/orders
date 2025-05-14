<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected SerializerInterface $_serializer,
        protected LoggerInterface     $logger,
    )
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Не логируем 404
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === Response::HTTP_NOT_FOUND) {
            $data = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
            $event->setResponse(new JsonResponse($data, Response::HTTP_NOT_FOUND));
            return;
        }

        $data = [
            'success' => false,
            'message' => sprintf(
                'ExceptionSubscriber Error: %s ',
                $exception->getMessage()
            ),
            'trace' => $exception->getTrace(),
        ];

        $env = $_ENV['APP_ENV'] ?? 'prod';
        $status = Response::HTTP_BAD_REQUEST;

        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
        } elseif (is_numeric($exception->getCode()) && $exception->getCode() > 99) {
            $status = $exception->getCode();
        }

        if ($status < 100 || $status > 599) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }


        $this->logger->error(
            sprintf(
                'ExceptionSubscriber Error: %s, in %s, on line %s with code %s',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $status
            ),
            [
                'environment' => $env,
                'status' => $status,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );

        $event->setResponse(new JsonResponse($data, $status));
    }

    public static function getSubscribedEvents(): array
    {
       return [
           KernelEvents::EXCEPTION => 'onKernelException',
       ];
    }
}