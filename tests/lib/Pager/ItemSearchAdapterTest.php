<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemSearchAdapter::class)]
final class ItemSearchAdapterTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    private SearchQuery $searchQuery;

    private ItemSearchAdapter $adapter;

    protected function setUp(): void
    {
        $this->backendStub = self::createStub(BackendInterface::class);
        $this->searchQuery = new SearchQuery('text');

        $this->adapter = new ItemSearchAdapter($this->backendStub, $this->searchQuery);
    }

    public function testGetNbResults(): void
    {
        $this->backendStub
            ->method('searchItemsCount')
            ->with(self::identicalTo($this->searchQuery))
            ->willReturn(3);

        self::assertSame(3, $this->adapter->getNbResults());
    }

    public function testGetSlice(): void
    {
        $items = [new Item(1), new Item(2), new Item(3)];

        $searchQuery = clone $this->searchQuery;
        $searchQuery->offset = 5;
        $searchQuery->limit = 10;

        $this->backendStub
            ->method('searchItems')
            ->with(self::equalTo($searchQuery))
            ->willReturn(new SearchResult($items));

        self::assertSame($items, $this->adapter->getSlice(5, 10));
    }
}
