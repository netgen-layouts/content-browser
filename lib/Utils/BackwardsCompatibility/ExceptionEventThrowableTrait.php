<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Utils\BackwardsCompatibility;

use Exception;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
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
    private function getThrowable(ExceptionEvent $event): Throwable
    {
        if (method_exists($event, 'getThrowable')) {
            return $event->getThrowable();
        }

        if (method_exists($event, 'getException')) {
            return $event->getException();
        }

        throw new RuntimeException('Event class missing getThrowable and getException methods.');
    }

    private function setThrowable(ExceptionEvent $event, Throwable $throwable): void
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
