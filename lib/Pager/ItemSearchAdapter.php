<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Netgen\ContentBrowser\Item\ItemInterface>
 */
final class ItemSearchAdapter implements AdapterInterface
{
    public function __construct(
        private BackendInterface $backend,
        private SearchQuery $searchQuery,
    ) {}

    public function getNbResults(): int
    {
        return $this->backend->searchItemsCount($this->searchQuery);
    }

    /**
     * @return iterable<int, \Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSlice(int $offset, int $length): iterable
    {
        // Cloning the query to replace offset & limit in the query with current values
        $searchQuery = clone $this->searchQuery;
        $searchQuery->offset = $offset;
        $searchQuery->limit = $length;

        return $this->backend->searchItems($searchQuery)->results;
    }
}
