<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;
use Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener;
use InvalidArgumentException;

class ExceptionConversionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new ExceptionConversionListener();

        self::assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 10)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsNotFoundException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException();

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            NotFoundHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsOutOfBoundsException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new OutOfBoundsException();

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            UnprocessableEntityHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsNonContentBrowserException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new InvalidArgumentException();

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            InvalidArgumentException::class,
            $event->getException()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new InvalidArgumentException();

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertEquals($exception, $event->getException());
    }
}
