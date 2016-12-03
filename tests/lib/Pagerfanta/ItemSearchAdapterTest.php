<?php

namespace Netgen\ContentBrowser\Tests\Pagerfanta;

use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter;
use PHPUnit\Framework\TestCase;

class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->adapter = new ItemSearchAdapter($this->itemRepositoryMock, 'text', 'value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->equalTo('text'), $this->equalTo('value'))
            ->will($this->returnValue(3));

        $this->assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('search')
            ->with(
                $this->equalTo('text'),
                $this->equalTo('value'),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
