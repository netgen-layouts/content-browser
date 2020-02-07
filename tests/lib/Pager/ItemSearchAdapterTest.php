<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Tests\Stubs\BackendInterface;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

final class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Backend\SearchQuery
     */
    private $searchQuery;

    /**
     * @var \Netgen\ContentBrowser\Pager\ItemSearchAdapter
     */
    private $adapter;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->searchQuery = new SearchQuery('text');

        $this->adapter = new ItemSearchAdapter($this->backendMock, $this->searchQuery);
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults(): void
    {
        $this->backendMock
            ->expects(self::once())
            ->method('searchItemsCount')
            ->with(self::identicalTo($this->searchQuery))
            ->willReturn(3);

        self::assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getSlice
     */
    public function testGetSlice(): void
    {
        $items = [new Item(1), new Item(2), new Item(3)];

        $searchQuery = clone $this->searchQuery;
        $searchQuery->setOffset(5);
        $searchQuery->setLimit(10);

        $this->backendMock
            ->expects(self::once())
            ->method('searchItems')
            ->with(self::equalTo($searchQuery))
            ->willReturn(new SearchResult($items));

        self::assertSame($items, $this->adapter->getSlice(5, 10));
    }
}
