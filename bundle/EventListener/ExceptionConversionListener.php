<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function is_a;

final class ExceptionConversionListener implements EventSubscriberInterface
{
    use ExceptionEventThrowableTrait;
    use MainRequestEventTrait;

    /**
     * @var array<class-string<\Throwable>, class-string<\Symfony\Component\HttpKernel\Exception\HttpException>>
     */
    private array $exceptionMap = [
        NotFoundException::class => NotFoundHttpException::class,
        OutOfBoundsException::class => UnprocessableEntityHttpException::class,
        InvalidArgumentException::class => BadRequestHttpException::class,
        // Various other useful exceptions
        AccessDeniedException::class => AccessDeniedHttpException::class,
    ];

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onException', 10]];
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $this->getThrowable($event);
        if ($exception instanceof HttpExceptionInterface) {
            return;
        }

        $exceptionClass = null;
        foreach ($this->exceptionMap as $sourceException => $targetException) {
            if (is_a($exception, $sourceException, true)) {
                $exceptionClass = $targetException;

                break;
            }
        }

        if ($exceptionClass !== null) {
            $convertedException = new $exceptionClass(
                $exception->getMessage(),
                $exception,
                $exception->getCode(),
            );

            $this->setThrowable($event, $convertedException);
        }
    }
}
