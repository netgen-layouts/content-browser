<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetBackendListener::class)]
final class SetBackendListenerTest extends TestCase
{
    private MockObject&BackendInterface $backendMock;

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

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [RequestEvent::class => ['onKernelRequest', 1]],
            $this->eventListener::getSubscribedEvents(),
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

        $this->eventListener->onKernelRequest($event);

        self::assertTrue($this->container->has('netgen_content_browser.backend'));
        self::assertSame($this->backendMock, $this->container->get('netgen_content_browser.backend'));
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

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
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

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
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

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }
}
