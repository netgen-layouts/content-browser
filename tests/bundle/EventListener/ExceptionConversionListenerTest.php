<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Netgen\ContentBrowser\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ExceptionConversionListenerTest extends TestCase
{
    use CreateEventTrait;
    use ExceptionEventThrowableTrait;

    private ExceptionConversionListener $eventListener;

    protected function setUp(): void
    {
        $this->eventListener = new ExceptionConversionListener();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 10]],
            $this->eventListener::getSubscribedEvents(),
        );
    }

    /**
     * @param class-string<\Symfony\Component\HttpKernel\Exception\HttpException> $convertedClass
     *
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     *
     * @dataProvider onExceptionDataProvider
     */
    public function testOnException(Exception $exception, string $convertedClass, int $statusCode, bool $converted): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->eventListener->onException($event);
        $eventException = $this->getThrowable($event);

        self::assertInstanceOf($convertedClass, $eventException);
        self::assertSame($exception->getMessage(), $eventException->getMessage());
        self::assertSame($exception->getCode(), $eventException->getCode());

        if ($eventException instanceof HttpExceptionInterface) {
            self::assertSame($statusCode, $eventException->getStatusCode());
        }

        $converted ?
            self::assertSame($exception, $eventException->getPrevious()) :
            self::assertNull($eventException->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $exception = new Exception('Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->eventListener->onException($event);
        $eventException = $this->getThrowable($event);

        self::assertSame($exception, $eventException);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $exception = new NotFoundException('Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception,
        );

        $this->eventListener->onException($event);
        $eventException = $this->getThrowable($event);

        self::assertSame($exception, $eventException);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInNonAPIRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->eventListener->onException($event);
        $eventException = $this->getThrowable($event);

        self::assertSame($exception, $eventException);
    }

    /**
     * @return array<int, mixed[]>
     */
    public function onExceptionDataProvider(): array
    {
        return [
            [
                new NotFoundException('Some error'),
                NotFoundHttpException::class,
                Response::HTTP_NOT_FOUND,
                true,
            ],
            [
                new InvalidArgumentException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ],
            [
                new OutOfBoundsException('Some error'),
                UnprocessableEntityHttpException::class,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                true,
            ],
            [
                new AccessDeniedException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                true,
            ],
            [
                new AccessDeniedHttpException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                false,
            ],
        ];
    }
}
