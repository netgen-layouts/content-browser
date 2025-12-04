<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetBackendListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetBackendListener::class)]
final class SetBackendListenerTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    private Container $container;

    private SetBackendListener $eventListener;

    protected function setUp(): void
    {
        $this->backendStub = self::createStub(BackendInterface::class);

        $this->container = new Container();

        $backendRegistry = new BackendRegistry(['item_type' => $this->backendStub]);

        $this->eventListener = new SetBackendListener(
            $this->container,
            $backendRegistry,
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new RequestEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertTrue($this->container->has('netgen_content_browser.backend'));
        self::assertSame($this->backendStub, $this->container->get('netgen_content_browser.backend'));
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new RequestEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }

    public function testOnKernelRequestWithNoItemType(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }

    public function testOnKernelRequestWithNoContentBrowserRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new RequestEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->eventListener->onKernelRequest($event);

        self::assertFalse($this->container->has('netgen_content_browser.backend'));
    }
}
