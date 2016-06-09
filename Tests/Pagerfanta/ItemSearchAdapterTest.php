<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter;

class ItemSearchAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ItemSearchAdapter($this->backendMock, 'text');
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->equalTo('text'))
            ->will($this->returnValue(3));

        self::assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemSearchAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('search')
            ->with(
                $this->equalTo('text'),
                $this->equalTo(array('offset' => 5, 'limit' => 10))
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('searchCount')
            ->with($this->equalTo('text'))
            ->will($this->returnValue(3));

        self::assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
