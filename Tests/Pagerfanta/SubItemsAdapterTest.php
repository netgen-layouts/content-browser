<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;

class SubItemsAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->adapter = new SubItemsAdapter($this->itemRepositoryMock, new Location(42));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(42)))
            ->will($this->returnValue(3));

        self::assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->itemRepositoryMock
            ->expects($this->at(0))
            ->method('getSubItems')
            ->with(
                $this->equalTo(new Location(42)),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->itemRepositoryMock
            ->expects($this->at(1))
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(42)))
            ->will($this->returnValue(3));

        self::assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
