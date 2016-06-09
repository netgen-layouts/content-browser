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
use PHPUnit\Framework\TestCase;

class ExceptionConversionListenerTest extends TestCase
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
     * @dataProvider onExceptionDataProvider
     *
     * @param \Exception $exception
     * @param string $convertedClass
     * @param int $statusCode
     */
    public function testOnException($exception, $convertedClass, $statusCode)
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->eventListener->onException($event);

        self::assertInstanceOf(
            $convertedClass,
            $event->getException()
        );

        self::assertEquals($statusCode, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
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
        $kernelMock = $this->createMock(HttpKernelInterface::class);
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

    public function onExceptionDataProvider()
    {
        return array(
            array(
                new NotFoundException('Some error'),
                NotFoundHttpException::class,
                Response::HTTP_NOT_FOUND,
            ),
            array(
                new InvalidArgumentException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
            ),
            array(
                new OutOfBoundsException('Some error'),
                UnprocessableEntityHttpException::class,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ),
            array(
                new AccessDeniedException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
            ),
        );
    }
}
