<?php

namespace App\EventSubscriber\OnKernelException;

use App\Exception\AuthService\AuthServiceUnauthorizedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthServiceUnauthorizedExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AuthServiceUnauthorizedException) {
            $response = new JsonResponse([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);

            $event->setResponse($response);
        }
    }
}
