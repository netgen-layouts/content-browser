<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter;

class ItemChildrenAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->backendMock = $this->getMock(BackendInterface::class);

        $this->adapter = new ItemChildrenAdapter($this->backendMock, 42);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getChildrenCount')
            ->with($this->equalTo(42))
            ->will($this->returnValue(3));

        self::assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getChildren')
            ->with(
                $this->equalTo(42),
                $this->equalTo(array('offset' => 5, 'limit' => 10))
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getChildrenCount')
            ->with($this->equalTo(42))
            ->will($this->returnValue(3));

        self::assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
