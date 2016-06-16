<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

class ValueChildrenAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ValueChildrenAdapter($this->backendMock, new Value(42, 42));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getChildrenCount')
            ->with($this->equalTo(new Value(42, 42)))
            ->will($this->returnValue(3));

        self::assertEquals(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter::getSlice
     */
    public function testGetSlice()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getChildren')
            ->with(
                $this->equalTo(new Value(42, 42)),
                $this->equalTo(array('offset' => 5, 'limit' => 10))
            )
            ->will($this->returnValue(array(1, 2, 3)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getChildrenCount')
            ->with($this->equalTo(new Value(42, 42)))
            ->will($this->returnValue(3));

        self::assertEquals(array(1, 2, 3), $this->adapter->getSlice(5, 10));
    }
}
