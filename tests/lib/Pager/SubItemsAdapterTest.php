<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pager\SubItemsAdapter;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;

final class SubItemsAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Pager\SubItemsAdapter
     */
    private $adapter;

    /**
     * @var \Netgen\ContentBrowser\Item\LocationInterface
     */
    private $location;

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->location = new Location(42);

        $this->adapter = new SubItemsAdapter($this->backendMock, $this->location);
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::getNbResults
     */
    public function testGetNbResults(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->identicalTo($this->location))
            ->will($this->returnValue(3));

        $this->assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::getSlice
     */
    public function testGetSlice(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItems')
            ->with(
                $this->identicalTo($this->location),
                $this->identicalTo(5),
                $this->identicalTo(10)
            )
            ->will($this->returnValue([1, 2, 3]));

        $this->assertSame([1, 2, 3], $this->adapter->getSlice(5, 10));
    }
}
