<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter;
use PHPUnit\Framework\TestCase;

class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->adapter = new ItemSearchAdapter($this->itemRepositoryMock, 'text', 'value');
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->equalTo('text'), $this->equalTo('value'))
            ->will($this->returnValue(3));

        self::assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->itemRepositoryMock
            ->expects($this->at(0))
            ->method('search')
            ->with(
                $this->equalTo('text'),
                $this->equalTo('value'),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->itemRepositoryMock
            ->expects($this->at(1))
            ->method('searchCount')
            ->with($this->equalTo('text'), $this->equalTo('value'))
            ->will($this->returnValue(3));

        self::assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
