<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use PHPUnit\Framework\TestCase;

final class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Pager\ItemSearchAdapter
     */
    private $adapter;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ItemSearchAdapter($this->backendMock, 'text');
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getNbResults
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
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getSlice
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
            ->will($this->returnValue([1, 2, 3]));

        $this->assertEquals([1, 2, 3], $this->adapter->getSlice(5, 10));
    }
}
