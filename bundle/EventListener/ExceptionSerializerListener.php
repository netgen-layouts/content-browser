<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Debug\Exception\FlattenException;
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
    private $outputDebugInfo = false;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct($outputDebugInfo = false, LoggerInterface $logger = null)
    {
        $this->outputDebugInfo = (bool) $outputDebugInfo;
        $this->logger = $logger ?: new NullLogger();
    }

    public static function getSubscribedEvents()
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [KernelEvents::EXCEPTION => ['onException', 5]];
    }

    /**
     * Serializes the exception.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $event->getException();

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
            $debugException = $exception;
            if ($exception->getPrevious() instanceof Exception) {
                $debugException = $exception->getPrevious();
            }

            $debugException = FlattenException::create($debugException);

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
     *
     * @param \Exception $exception
     */
    private function logException(Exception $exception)
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
