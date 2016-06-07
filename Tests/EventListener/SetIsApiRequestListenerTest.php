<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class SetIsApiRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new SetIsApiRequestListener();

        self::assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', 30)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'netgen_content_browser_api_v1_config');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        self::assertEquals(
            true,
            $event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        self::assertEquals(
            false,
            $event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $eventListener->onKernelRequest($event);

        self::assertEquals(
            false,
            $event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }
}
