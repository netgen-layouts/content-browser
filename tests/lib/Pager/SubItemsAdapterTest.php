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

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new SubItemsAdapter($this->backendMock, new Location(42));
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(42)))
            ->will($this->returnValue(3));

        $this->assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\SubItemsAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItems')
            ->with(
                $this->equalTo(new Location(42)),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue([1, 2, 3]));

        $this->assertEquals([1, 2, 3], $this->adapter->getSlice(5, 10));
    }
}
