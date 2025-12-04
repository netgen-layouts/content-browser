<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pager\SubItemsAdapter;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubItemsAdapter::class)]
final class SubItemsAdapterTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    private SubItemsAdapter $adapter;

    private Location $location;

    protected function setUp(): void
    {
        $this->backendStub = self::createStub(BackendInterface::class);
        $this->location = new Location(42);

        $this->adapter = new SubItemsAdapter($this->backendStub, $this->location);
    }

    public function testGetNbResults(): void
    {
        $this->backendStub
            ->method('getSubItemsCount')
            ->with(self::identicalTo($this->location))
            ->willReturn(3);

        self::assertSame(3, $this->adapter->getNbResults());
    }

    public function testGetSlice(): void
    {
        $items = [new Item(1), new Item(2), new Item(3)];

        $this->backendStub
            ->method('getSubItems')
            ->with(
                self::identicalTo($this->location),
                self::identicalTo(5),
                self::identicalTo(10),
            )
            ->willReturn($items);

        self::assertSame($items, $this->adapter->getSlice(5, 10));
    }
}
