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

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ItemSearchAdapter($this->backendMock, 'text');
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->identicalTo('text'))
            ->will($this->returnValue(3));

        $this->assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getSlice
     */
    public function testGetSlice(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('search')
            ->with(
                $this->identicalTo('text'),
                $this->identicalTo(5),
                $this->identicalTo(10)
            )
            ->will($this->returnValue([1, 2, 3]));

        $this->assertSame([1, 2, 3], $this->adapter->getSlice(5, 10));
    }
}
