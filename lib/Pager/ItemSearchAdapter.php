<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Pagerfanta\Adapter\AdapterInterface;
use function get_debug_type;
use function method_exists;
use function trigger_deprecation;

final class ItemSearchAdapter implements AdapterInterface
{
    private BackendInterface $backend;

    private SearchQuery $searchQuery;

    public function __construct(BackendInterface $backend, SearchQuery $searchQuery)
    {
        $this->backend = $backend;
        $this->searchQuery = $searchQuery;
    }

    public function getNbResults(): int
    {
        if (method_exists($this->backend, 'searchItemsCount')) {
            return $this->backend->searchItemsCount($this->searchQuery);
        }

        trigger_deprecation('netgen/content-browser', '1.2', '"%s::searchCount" method is deprecated. Implement the "searchItemsCount" method instead.', get_debug_type($this->backend));

        return $this->backend->searchCount($this->searchQuery->getSearchText());
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSlice($offset, $length): iterable
    {
        if (method_exists($this->backend, 'searchItems')) {
            // Cloning the query to replace offset & limit in the query with current values
            $searchQuery = clone $this->searchQuery;
            $searchQuery->setOffset($offset);
            $searchQuery->setLimit($length);

            return $this->backend->searchItems($searchQuery)->getResults();
        }

        trigger_deprecation('netgen/content-browser', '1.2', '"%s::search" method is deprecated. Implement the "searchItems" method instead.', get_debug_type($this->backend));

        return $this->backend->search($this->searchQuery->getSearchText(), $offset, $length);
    }
}
