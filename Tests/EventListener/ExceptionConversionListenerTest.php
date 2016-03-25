<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionConversionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener
     */
    protected $eventListener;

    public function setUp()
    {
        $this->eventListener = new ExceptionConversionListener();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 10)),
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsNotFoundException()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

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
    public function testOnExceptionConvertsInvalidArgumentException()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new InvalidArgumentException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        self::assertInstanceOf(
            BadRequestHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsOutOfBoundsException()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new OutOfBoundsException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

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
    public function testOnExceptionConvertsAccessDeniedException()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new AccessDeniedException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        self::assertInstanceOf(
            AccessDeniedHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_FORBIDDEN, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new Exception('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        self::assertEquals($exception, $event->getException());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        self::assertEquals($exception, $event->getException());
    }
}
