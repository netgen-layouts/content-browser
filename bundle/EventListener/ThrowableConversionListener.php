<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use function is_a;

final class ThrowableConversionListener implements EventSubscriberInterface
{
    /**
     * @var array<class-string<\Throwable>, class-string<\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface>>
     */
    private array $throwableMap = [
        NotFoundException::class => NotFoundHttpException::class,
        OutOfBoundsException::class => UnprocessableEntityHttpException::class,
        InvalidArgumentException::class => BadRequestHttpException::class,
        // Various other useful throwables
        AccessDeniedException::class => AccessDeniedHttpException::class,
    ];

    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => ['onException', 10]];
    }

    /**
     * Converts throwables to Symfony HTTP exceptions.
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
        if ($throwable instanceof HttpExceptionInterface) {
            return;
        }

        $throwableClass = null;
        foreach ($this->throwableMap as $sourceThrowable => $targetThrowable) {
            if (is_a($throwable, $sourceThrowable, true)) {
                $throwableClass = $targetThrowable;

                break;
            }
        }

        if ($throwableClass !== null) {
            $convertedThrowable = new $throwableClass(
                $throwable->getMessage(),
                $throwable,
                $throwable->getCode(),
            );

            $event->setThrowable($convertedThrowable);
        }
    }
}
