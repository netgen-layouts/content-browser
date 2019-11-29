<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Tests\Stubs\Item;
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

    protected function setUp(): void
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
            ->expects(self::once())
            ->method('searchCount')
            ->with(self::identicalTo('text'))
            ->willReturn(3);

        self::assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getSlice
     */
    public function testGetSlice(): void
    {
        $items = [new Item(1), new Item(2), new Item(3)];

        $this->backendMock
            ->expects(self::once())
            ->method('search')
            ->with(
                self::identicalTo('text'),
                self::identicalTo(5),
                self::identicalTo(10)
            )
            ->willReturn($items);

        self::assertSame($items, $this->adapter->getSlice(5, 10));
    }
}
