<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\ThrowableConversionListener;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

#[CoversClass(ThrowableConversionListener::class)]
final class ThrowableConversionListenerTest extends TestCase
{
    private ThrowableConversionListener $eventListener;

    protected function setUp(): void
    {
        $this->eventListener = new ThrowableConversionListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ExceptionEvent::class => ['onException', 10]],
            $this->eventListener::getSubscribedEvents(),
        );
    }

    /**
     * @param class-string<\Symfony\Component\HttpKernel\Exception\HttpException> $convertedClass
     */
    #[DataProvider('onExceptionDataProvider')]
    public function testOnException(Throwable $throwable, string $convertedClass, int $statusCode, bool $converted): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->eventListener->onException($event);
        $eventThrowable = $event->getThrowable();

        self::assertInstanceOf($convertedClass, $eventThrowable);
        self::assertSame($throwable->getMessage(), $eventThrowable->getMessage());
        self::assertSame($throwable->getCode(), $eventThrowable->getCode());
        self::assertSame($statusCode, $eventThrowable->getStatusCode());

        $converted ?
            self::assertSame($throwable, $eventThrowable->getPrevious()) :
            self::assertNull($eventThrowable->getPrevious());
    }

    public function testOnExceptionNotConvertsOtherThrowables(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $throwable = new Exception('Some error');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->eventListener->onException($event);
        $eventThrowable = $event->getThrowable();

        self::assertSame($throwable, $eventThrowable);
    }

    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $throwable = new NotFoundException('Some error');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $throwable,
        );

        $this->eventListener->onException($event);
        $eventThrowable = $event->getThrowable();

        self::assertSame($throwable, $eventThrowable);
    }

    public function testOnExceptionInNonAPIRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $throwable = new NotFoundException('Some error');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->eventListener->onException($event);
        $eventThrowable = $event->getThrowable();

        self::assertSame($throwable, $eventThrowable);
    }

    /**
     * @return array<int, mixed[]>
     */
    public static function onExceptionDataProvider(): iterable
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
