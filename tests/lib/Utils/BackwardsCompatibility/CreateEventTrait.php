<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Utils\BackwardsCompatibility;

use Exception;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use function class_exists;

/**
 * @deprecated Remove when support for Symfony 3.4 ends.
 *
 * Trait that enables test to use both deprecated events and new ones implemented in Symfony 4.3.
 */
trait CreateEventTrait
{
    /**
     * @return \Symfony\Component\HttpKernel\Event\RequestEvent
     */
    private function createRequestEvent(HttpKernelInterface $kernel, Request $request, int $requestType): object
    {
        if (class_exists(RequestEvent::class)) {
            return new RequestEvent($kernel, $request, $requestType);
        }

        if (class_exists(GetResponseEvent::class)) {
            return new GetResponseEvent($kernel, $request, $requestType);
        }

        throw new RuntimeException('Missing RequestEvent and GetResponseEvent classes');
    }

    /**
     * @param \Throwable|\Exception $throwable
     *
     * @return \Symfony\Component\HttpKernel\Event\ExceptionEvent
     */
    private function createExceptionEvent(HttpKernelInterface $kernel, Request $request, int $requestType, $throwable): object
    {
        if (class_exists(ExceptionEvent::class)) {
            return new ExceptionEvent($kernel, $request, $requestType, $throwable);
        }

        if ($throwable instanceof Exception && class_exists(GetResponseForExceptionEvent::class)) {
            return new GetResponseForExceptionEvent($kernel, $request, $requestType, $throwable);
        }

        throw new RuntimeException('Missing ExceptionEvent and GetResponseForExceptionEvent classes');
    }
}
