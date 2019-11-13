<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\ErrorRenderer\Exception\FlattenException as ErrorRendererFlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSerializerListener implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $outputDebugInfo;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(bool $outputDebugInfo, ?LoggerInterface $logger = null)
    {
        $this->outputDebugInfo = $outputDebugInfo;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [KernelEvents::EXCEPTION => ['onException', 5]];
    }

    /**
     * Serializes the exception.
     */
    public function onException(GetResponseForExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        /** @deprecated Remove call to getException when support for Symfony 3.4 ends */
        $exception = method_exists($event, 'getThrowable') ? $event->getThrowable() : $event->getException();

        $this->logException($exception);

        $data = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $debugException = $exception->getPrevious() ?? $exception;
            if (class_exists(ErrorRendererFlattenException::class)) {
                $debugException = ErrorRendererFlattenException::createFromThrowable($debugException);
            } elseif ($debugException instanceof Exception) {
                $debugException = DebugFlattenException::create($debugException);
            }

            $data['debug'] = [
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $debugException->getTrace(),
            ];
        }

        $event->setResponse(new JsonResponse($data));
    }

    /**
     * Logs all critical errors.
     */
    private function logException(Exception $exception): void
    {
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            return;
        }

        $this->logger->critical(
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ),
            ['exception' => $exception]
        );
    }
}
