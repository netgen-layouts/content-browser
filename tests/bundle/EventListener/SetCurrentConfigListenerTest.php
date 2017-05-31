<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Config\ConfigLoaderInterface;
use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCurrentConfigListenerTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener
     */
    protected $eventListener;

    public function setUp()
    {
        $this->configLoaderMock = $this->createMock(ConfigLoaderInterface::class);
        $this->containerMock = $this->createMock(ContainerInterface::class);

        $this->eventListener = new SetCurrentConfigListener(
            $this->containerMock,
            $this->configLoaderMock
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $config = new Configuration('value');

        $this->configLoaderMock
            ->expects($this->at(0))
            ->method('loadConfig')
            ->with($this->equalTo('item_type'))
            ->will($this->returnValue($config));

        $this->containerMock
            ->expects($this->at(0))
            ->method('set')
            ->with(
                $this->equalTo('netgen_content_browser.current_config'),
                $this->equalTo($config)
            );

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoItemType()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoContentBrowserRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }
}
