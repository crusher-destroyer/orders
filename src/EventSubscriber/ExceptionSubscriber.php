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

        $data = [
            'success' => false,
            'message' => sprintf(
                'ExceptionSubscriber Error: %s with code: %s',
                $exception->getMessage(),
                $exception->getCode()
            ),
        ];

        $status = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $env = $_ENV['APP_ENV'] ?? 'prod';

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