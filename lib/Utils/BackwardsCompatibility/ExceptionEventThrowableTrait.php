<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Utils\BackwardsCompatibility;

use Exception;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Throwable;

use function method_exists;

/**
 * @deprecated Remove when support for Symfony 3.4 ends.
 *
 * Trait that supports (get|set)Throwable and (get|set)Exception in exception
 * events.
 */
trait ExceptionEventThrowableTrait
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    private function getThrowable($event): Throwable
    {
        if (method_exists($event, 'getThrowable')) {
            return $event->getThrowable();
        }

        if (method_exists($event, 'getException')) {
            return $event->getException();
        }

        throw new RuntimeException('Event class missing getThrowable and getException methods.');
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    private function setThrowable($event, Throwable $throwable): void
    {
        if (method_exists($event, 'setThrowable')) {
            $event->setThrowable($throwable);

            return;
        }

        if ($throwable instanceof Exception && method_exists($event, 'setException')) {
            $event->setException($throwable);
        }
    }
}
