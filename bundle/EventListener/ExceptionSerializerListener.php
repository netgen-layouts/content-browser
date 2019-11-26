<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Exception;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\ErrorHandler\Exception\FlattenException as ErrorHandlerFlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class ExceptionSerializerListener implements EventSubscriberInterface
{
    use ExceptionEventThrowableTrait;

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
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $this->getThrowable($event);
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
            if (class_exists(ErrorHandlerFlattenException::class)) {
                $debugException = ErrorHandlerFlattenException::createFromThrowable($debugException);
            } elseif ($debugException instanceof Exception && class_exists(DebugFlattenException::class)) {
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
    private function logException(Throwable $error): void
    {
        if ($error instanceof HttpExceptionInterface && $error->getStatusCode() < 500) {
            return;
        }

        $this->logger->critical(
            sprintf(
                'Uncaught PHP error %s: "%s" at %s line %s',
                get_class($error),
                $error->getMessage(),
                $error->getFile(),
                $error->getLine()
            ),
            ['error' => $error]
        );
    }
}
