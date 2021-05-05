<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetConfigListenerTest extends TestCase
{
    use CreateEventTrait;

    private Container $container;

    private SetConfigListener $eventListener;

    protected function setUp(): void
    {
        $this->container = new Container();

        $this->eventListener = new SetConfigListener(
            $this->container,
            $this->createMock(EventDispatcherInterface::class),
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->eventListener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);

        self::assertTrue($this->container->has('netgen_content_browser.config'));
        self::assertSame($config, $this->container->get('netgen_content_browser.config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithCustomParams(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');
        $request->query->set('customParams', ['custom' => 'value', 'two' => 'override']);

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $config->setParameter('one', 'default');
        $config->setParameter('two', 'default');

        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);

        self::assertTrue($config->hasParameter('one'));
        self::assertSame('default', $config->getParameter('one'));

        self::assertTrue($config->hasParameter('two'));
        self::assertSame('override', $config->getParameter('two'));

        self::assertTrue($config->hasParameter('custom'));
        self::assertSame('value', $config->getParameter('custom'));

        self::assertTrue($this->container->has('netgen_content_browser.config'));
        self::assertSame($config, $this->container->get('netgen_content_browser.config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonArrayCustomParams(): void
    {
        if (Kernel::VERSION_ID >= 50100) {
            self::markTestSkipped('Test not needed on Symfony 5.1+');
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid custom parameters specification for "item_type" item type.');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');
        $request->query->set('customParams', 'custom');

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $this->container->set('netgen_content_browser.config.item_type', new Configuration('value', 'Value', []));

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestThrowsInvalidArgumentExceptionWithInvalidConfigService(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration for "item_type" item type is invalid.');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $config = new stdClass();
        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoItemType(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoContentBrowserRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.config'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidItemTypeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration for "unknown" item type does not exist.');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'unknown');

        $event = $this->createRequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $this->container->set('netgen_content_browser.config.item_type', $config);

        $this->eventListener->onKernelRequest($event);
    }
}
