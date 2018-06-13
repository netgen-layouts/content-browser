<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetCurrentConfigListenerTest extends TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener
     */
    private $eventListener;

    public function setUp()
    {
        $this->container = new Container();

        $this->eventListener = new SetCurrentConfigListener($this->container);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::loadConfig
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
        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);

        $this->assertTrue($this->container->has('netgen_content_browser.current_config'));
        $this->assertEquals($config, $this->container->get('netgen_content_browser.current_config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithCustomParams()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');
        $request->query->set('customParams', ['custom' => 'value', 'two' => 'override']);

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $config = new Configuration('value');
        $config->setParameter('one', 'default');
        $config->setParameter('two', 'default');

        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);

        $this->assertTrue($config->hasParameter('one'));
        $this->assertEquals('default', $config->getParameter('one'));

        $this->assertTrue($config->hasParameter('two'));
        $this->assertEquals('override', $config->getParameter('two'));

        $this->assertTrue($config->hasParameter('custom'));
        $this->assertEquals('value', $config->getParameter('custom'));

        $this->assertTrue($this->container->has('netgen_content_browser.current_config'));
        $this->assertEquals($config, $this->container->get('netgen_content_browser.current_config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Configuration for "item_type" item type is invalid.
     */
    public function testOnKernelRequestThrowsInvalidArgumentExceptionWithInvalidConfigService()
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

        $config = new stdClass();
        $this->container->set('netgen_content_browser.config.item_type', $config);

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

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_config'));
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

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_config'));
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

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Configuration for "unknown" item type does not exist.
     */
    public function testOnKernelRequestWithInvalidItemTypeThrowsInvalidArgumentException()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'unknown');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $config = new Configuration('value');
        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);
    }
}
