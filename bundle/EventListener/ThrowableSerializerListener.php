<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

use function array_key_exists;
use function sprintf;

final class ThrowableSerializerListener implements EventSubscriberInterface
{
    public function __construct(
        private bool $outputDebugInfo,
        private LoggerInterface $logger = new NullLogger(),
    ) {}

    public static function getSubscribedEvents(): array
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [ExceptionEvent::class => ['onException', 5]];
    }

    /**
     * Serializes the throwable.
     */
    public function onException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $throwable = $event->getThrowable();
        $this->logThrowable($throwable);

        $data = [
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
        ];

        if ($throwable instanceof HttpExceptionInterface) {
            $statusCode = $throwable->getStatusCode();
            if (array_key_exists($statusCode, Response::$statusTexts)) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $flattenException = FlattenException::createFromThrowable($throwable->getPrevious() ?? $throwable);

            $data['debug'] = [
                'file' => $flattenException->getFile(),
                'line' => $flattenException->getLine(),
                'trace' => $flattenException->getTrace(),
            ];
        }

        $event->setResponse(new JsonResponse($data));
    }

    /**
     * Logs all critical errors.
     */
    private function logThrowable(Throwable $throwable): void
    {
        if ($throwable instanceof HttpExceptionInterface && $throwable->getStatusCode() < 500) {
            return;
        }

        $this->logger->critical(
            sprintf(
                'Uncaught PHP error %s: "%s" at %s line %s',
                $throwable::class,
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine(),
            ),
            ['error' => $throwable],
        );
    }
}
