<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSerializerListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener
     */
    private $eventListener;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Psr\Log\LoggerInterface
     */
    private $loggerMock;

    public function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->eventListener = new ExceptionSerializerListener(false, $this->loggerMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onException', 5]],
            $this->eventListener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::logException
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException(): void
    {
        $exception = new NotFoundHttpException('Some message');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ],
            json_decode($event->getResponse()->getContent(), true)
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::logException
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionLogsCriticalError(): void
    {
        $exception = new RuntimeException('Some message');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->loggerMock
            ->expects($this->once())
            ->method('critical');

        $this->eventListener->onException($event);

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ],
            json_decode($event->getResponse()->getContent(), true)
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::logException
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithDebugging(): void
    {
        $exception = new NotFoundHttpException('Some message', new Exception('Previous exception'));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener = new ExceptionSerializerListener(true, $this->loggerMock);
        $this->eventListener->onException($event);

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $data = json_decode($event->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status_code', $data);
        $this->assertArrayHasKey('status_text', $data);
        $this->assertArrayHasKey('debug', $data);
        $this->assertArrayHasKey('line', $data['debug']);
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('trace', $data['debug']);

        $this->assertSame($exception->getCode(), $data['code']);
        $this->assertSame($exception->getMessage(), $data['message']);
        $this->assertSame($exception->getStatusCode(), $data['status_code']);
        $this->assertSame(Response::$statusTexts[$exception->getStatusCode()], $data['status_text']);
        $this->assertSame(__FILE__, $data['debug']['file']);
        $this->assertGreaterThan(0, $data['debug']['line']);
        $this->assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new NotFoundHttpException('Some message')
        );

        $this->eventListener->onException($event);

        $this->assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoContentBrowserRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('Some message')
        );

        $this->eventListener->onException($event);

        $this->assertFalse($event->hasResponse());
    }
}
