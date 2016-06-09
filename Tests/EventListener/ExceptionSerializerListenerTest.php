<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class ExceptionSerializerListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener
     */
    protected $eventListener;

    public function setUp()
    {
        $this->eventListener = new ExceptionSerializerListener();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 5)),
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException()
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

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        self::assertEquals(
            array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ),
            json_decode($event->getResponse()->getContent(), true)
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::setOutputDebugInfo
     */
    public function testOnExceptionWithDebugging()
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

        $this->eventListener->setOutputDebugInfo(true);
        $this->eventListener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $data = json_decode($event->getResponse()->getContent(), true);

        self::assertInternalType('array', $data);
        self::assertArrayHasKey('code', $data);
        self::assertArrayHasKey('message', $data);
        self::assertArrayHasKey('status_code', $data);
        self::assertArrayHasKey('status_text', $data);
        self::assertArrayHasKey('debug', $data);
        self::assertArrayHasKey('line', $data['debug']);
        self::assertArrayHasKey('file', $data['debug']);
        self::assertArrayHasKey('trace', $data['debug']);

        self::assertEquals($exception->getCode(), $data['code']);
        self::assertEquals($exception->getMessage(), $data['message']);
        self::assertEquals($exception->getStatusCode(), $data['status_code']);
        self::assertEquals(Response::$statusTexts[$exception->getStatusCode()], $data['status_text']);
        self::assertEquals(__FILE__, $data['debug']['file']);
        self::assertGreaterThan(0, $data['debug']['line']);
        self::assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest()
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

        self::assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoContentBrowserRequest()
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

        self::assertNull($event->getResponse());
    }
}
