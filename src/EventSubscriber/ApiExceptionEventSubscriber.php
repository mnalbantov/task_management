<?php

namespace App\EventSubscriber;

use App\Exception\ViolationException;
use App\Response\Error\ViolationResponseHandlerInterface;
use App\Response\ErrorResponse;
use App\Response\NotFoundResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private ViolationResponseHandlerInterface $violationResponseHandler;

    public function __construct(
        LoggerInterface $logger,
        ViolationResponseHandlerInterface $violationResponseHandler
    ) {
        $this->logger = $logger;
        $this->violationResponseHandler = $violationResponseHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ViolationException) {
            $errors = $this->violationResponseHandler->handleViolationResponse($exception->getViolationErrors());
            $response = new ErrorResponse($errors);
            $event->setResponse($response);
        }
        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new NotFoundResponse());
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response instanceof ErrorResponse) {
            $response->setStatusCode(Response::HTTP_OK); // by design we want all responses to be 200
        }
    }
}