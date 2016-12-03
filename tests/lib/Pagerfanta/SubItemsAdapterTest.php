<?php

namespace Netgen\ContentBrowser\Tests\Pagerfanta;

use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;

class SubItemsAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->adapter = new SubItemsAdapter($this->itemRepositoryMock, new Location(42));
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(42)))
            ->will($this->returnValue(3));

        $this->assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('getSubItems')
            ->with(
                $this->equalTo(new Location(42)),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
