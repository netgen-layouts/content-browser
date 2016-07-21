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
use PHPUnit\Framework\TestCase;

class ExceptionSerializerListenerTest extends TestCase
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
        $this->assertEquals(
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

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertEquals(
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

        $this->assertEquals($exception->getCode(), $data['code']);
        $this->assertEquals($exception->getMessage(), $data['message']);
        $this->assertEquals($exception->getStatusCode(), $data['status_code']);
        $this->assertEquals(Response::$statusTexts[$exception->getStatusCode()], $data['status_text']);
        $this->assertEquals(__FILE__, $data['debug']['file']);
        $this->assertGreaterThan(0, $data['debug']['line']);
        $this->assertNotEmpty($data['debug']['trace']);
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

        $this->assertNull($event->getResponse());
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

        $this->assertNull($event->getResponse());
    }
}
