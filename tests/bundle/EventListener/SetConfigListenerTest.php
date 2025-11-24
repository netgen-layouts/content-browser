<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetConfigListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetConfigListener::class)]
final class SetConfigListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $eventListener = new SetConfigListener(
            new Container(),
            new ConfigRegistry([]),
            $this->createMock(EventDispatcherInterface::class),
        );

        self::assertSame(
            [RequestEvent::class => ['onKernelRequest', 1]],
            $eventListener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $configRegistry = new ConfigRegistry(['item_type' => $config]);

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            $configRegistry,
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);

        self::assertTrue($container->has('netgen_content_browser.config'));
        self::assertSame($config, $container->get('netgen_content_browser.config'));
    }

    public function testOnKernelRequestWithCustomParams(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');
        $request->query->set('customParams', ['custom' => 'value', 'two' => 'override']);

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $config->setParameter('one', 'default');
        $config->setParameter('two', 'default');

        $configRegistry = new ConfigRegistry(['item_type' => $config]);

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            $configRegistry,
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);

        self::assertTrue($config->hasParameter('one'));
        self::assertSame('default', $config->getParameter('one'));

        self::assertTrue($config->hasParameter('two'));
        self::assertSame('override', $config->getParameter('two'));

        self::assertTrue($config->hasParameter('custom'));
        self::assertSame('value', $config->getParameter('custom'));

        self::assertTrue($container->has('netgen_content_browser.config'));
        self::assertSame($config, $container->get('netgen_content_browser.config'));
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
        );

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            new ConfigRegistry([]),
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);

        self::assertFalse($container->has('netgen_content_browser.config'));
    }

    public function testOnKernelRequestWithNoItemType(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            new ConfigRegistry([]),
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);

        self::assertFalse($container->has('netgen_content_browser.config'));
    }

    public function testOnKernelRequestWithNoContentBrowserRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            new ConfigRegistry([]),
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);

        self::assertFalse($container->has('netgen_content_browser.config'));
    }

    public function testOnKernelRequestWithInvalidItemTypeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration for item type "unknown" does not exist.');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'unknown');

        $event = new RequestEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $config = new Configuration('value', 'Value', []);
        $configRegistry = new ConfigRegistry(['item_type' => $config]);

        $container = new Container();

        $eventListener = new SetConfigListener(
            $container,
            $configRegistry,
            $this->createMock(EventDispatcherInterface::class),
        );

        $eventListener->onKernelRequest($event);
    }
}
