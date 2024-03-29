<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetBackendListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $backendMock;

    private Container $container;

    private BackendRegistry $backendRegistry;

    private SetBackendListener $eventListener;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->container = new Container();
        $this->backendRegistry = new BackendRegistry(['item_type' => $this->backendMock]);

        $this->eventListener = new SetBackendListener(
            $this->container,
            $this->backendRegistry,
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 1]],
            $this->eventListener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::onKernelRequest
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

        $this->eventListener->onKernelRequest($event);

        self::assertTrue($this->container->has('netgen_content_browser.backend'));
        self::assertSame($this->backendMock, $this->container->get('netgen_content_browser.backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::onKernelRequest
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

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::onKernelRequest
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

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener::onKernelRequest
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

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }
}
