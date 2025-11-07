<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ValueResolver;

use Netgen\Bundle\ContentBrowserBundle\ValueResolver\LocationValueResolver;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Kernel;

#[CoversClass(LocationValueResolver::class)]
final class LocationValueResolverTest extends TestCase
{
    private MockObject $backendMock;

    private LocationValueResolver $valueResolver;

    protected function setUp(): void
    {
        if (Kernel::VERSION_ID < 60400) {
            self::markTestSkipped('Test requires Symfony 6.4 to run');
        }

        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        $this->valueResolver = new LocationValueResolver($backendRegistry);
    }

    public function testResolve(): void
    {
        $argument = new ArgumentMetadata('location', LocationInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('itemType', 'value');

        $location = new Location(42);

        $this->backendMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertSame($location, $values[0]);
    }

    public function testResolveWithMissingLocationId(): void
    {
        $argument = new ArgumentMetadata('location', LocationInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadLocation');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }

    public function testResolveWithMissingItemType(): void
    {
        $argument = new ArgumentMetadata('location', LocationInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $this->backendMock
            ->expects(self::never())
            ->method('loadLocation');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }

    public function testResolveWithEmptyRequiredLocationId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute "locationId" is empty');

        $argument = new ArgumentMetadata('location', LocationInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadLocation');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }
}
