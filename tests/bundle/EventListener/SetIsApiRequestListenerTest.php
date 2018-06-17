<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetIsApiRequestListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener
     */
    private $eventListener;

    public function setUp(): void
    {
        $this->eventListener = new SetIsApiRequestListener();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => ['onKernelRequest', 30]],
            $this->eventListener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngcb_api_v1_config');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->eventListener->onKernelRequest($event);

        $this->assertTrue($event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME));
    }
}
