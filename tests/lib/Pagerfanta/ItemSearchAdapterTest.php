<?php

namespace Netgen\ContentBrowser\Tests\Pagerfanta;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter;
use PHPUnit\Framework\TestCase;

class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter
     */
    private $adapter;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ItemSearchAdapter($this->backendMock, 'text');
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->equalTo('text'))
            ->will($this->returnValue(3));

        $this->assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('search')
            ->with(
                $this->equalTo('text'),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
