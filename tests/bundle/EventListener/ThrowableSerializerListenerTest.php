<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\ThrowableSerializerListener;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use function json_decode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(ThrowableSerializerListener::class)]
final class ThrowableSerializerListenerTest extends TestCase
{
    private ThrowableSerializerListener $eventListener;

    private MockObject&LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->eventListener = new ThrowableSerializerListener(false, $this->loggerMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 5]],
            $this->eventListener::getSubscribedEvents(),
        );
    }

    public function testOnException(): void
    {
        $throwable = new NotFoundHttpException('Some message');

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

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            [
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
                'status_code' => $throwable->getStatusCode(),
                'status_text' => Response::$statusTexts[$throwable->getStatusCode()],
            ],
            json_decode((string) $event->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testOnExceptionLogsCriticalError(): void
    {
        $throwable = new RuntimeException('Some message');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->loggerMock
            ->expects(self::once())
            ->method('critical');

        $this->eventListener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            [
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
            ],
            json_decode((string) $event->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testOnExceptionWithDebugging(): void
    {
        $throwable = new NotFoundHttpException('Some message', new Exception('Previous exception'));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->eventListener = new ThrowableSerializerListener(true, $this->loggerMock);
        $this->eventListener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        $data = json_decode((string) $event->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertArrayHasKey('code', $data);
        self::assertArrayHasKey('message', $data);
        self::assertArrayHasKey('status_code', $data);
        self::assertArrayHasKey('status_text', $data);
        self::assertArrayHasKey('debug', $data);
        self::assertArrayHasKey('line', $data['debug']);
        self::assertArrayHasKey('file', $data['debug']);
        self::assertArrayHasKey('trace', $data['debug']);

        self::assertSame($throwable->getCode(), $data['code']);
        self::assertSame($throwable->getMessage(), $data['message']);
        self::assertSame($throwable->getStatusCode(), $data['status_code']);
        self::assertSame(Response::$statusTexts[$throwable->getStatusCode()], $data['status_text']);
        self::assertSame(__FILE__, $data['debug']['file']);
        self::assertGreaterThan(0, $data['debug']['line']);
        self::assertNotEmpty($data['debug']['trace']);
    }

    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new NotFoundHttpException('Some message'),
        );

        $this->eventListener->onException($event);

        self::assertFalse($event->hasResponse());
    }

    public function testOnExceptionWithNoContentBrowserRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new NotFoundHttpException('Some message'),
        );

        $this->eventListener->onException($event);

        self::assertFalse($event->hasResponse());
    }
}
