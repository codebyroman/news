<?php

namespace App\EventListener;

use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'prepareJsonResponse', priority: 10)]
class ExceptionListener
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly bool $isDebug
    )
    {
    }

    public function prepareJsonResponse(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $code = $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($this->isDebug) {
            $message = $throwable->getMessage();
            $details = new ErrorDebugDetails($throwable->getTraceAsString());
        } else {
            $message = Response::$statusTexts[$code];
            $details = null;
        }

        $data = $this->serializer->serialize(new ErrorResponse($message, $details), JsonEncoder::FORMAT);

        $event->setResponse(new JsonResponse($data, $code, [], true));
    }
}
