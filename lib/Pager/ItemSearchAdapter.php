<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Pagerfanta\Adapter\AdapterInterface;

final class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Backend\SearchQuery
     */
    private $searchQuery;

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

        @trigger_error(sprintf('"%s::searchCount" method is deprecated in 1.2 and will be removed in 2.0. Implement the "searchItemsCount" method instead.', get_class($this->backend)), E_USER_DEPRECATED);

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

        @trigger_error(sprintf('"%s::search" method is deprecated in 1.2 and will be removed in 2.0. Implement the "searchItems" method instead.', get_class($this->backend)), E_USER_DEPRECATED);

        return $this->backend->search($this->searchQuery->getSearchText(), $offset, $length);
    }
}
