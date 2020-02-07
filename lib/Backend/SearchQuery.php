<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Netgen\ContentBrowser\Item\LocationInterface;

final class SearchQuery
{
    /**
     * @var string
     */
    private $searchText;

    /**
     * @var \Netgen\ContentBrowser\Item\LocationInterface|null
     */
    private $location;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var int
     */
    private $limit = 25;

    public function __construct(string $searchText, LocationInterface $location = null)
    {
        $this->searchText = $searchText;
        $this->location = $location;
    }

    /**
     * Returns the search text in the query.
     */
    public function getSearchText(): string
    {
        return $this->searchText;
    }

    /**
     * Returns the location in which to perform the search.
     */
    public function getLocation(): ?LocationInterface
    {
        return $this->location;
    }

    /**
     * Returns the offset with which the search is performed.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Sets the offset with which the search is performed.
     *
     * Must be equal or larger than zero.
     */
    public function setOffset(int $offset): self
    {
        if ($offset < 0) {
            throw new OutOfBoundsException('Search offset must be an integer equal or larger than zero.');
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * Returns the limit with which the search is performed.
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Sets the limit with which the search is performed.
     *
     * Must be larger than zero.
     */
    public function setLimit(int $limit): self
    {
        if ($limit <= 0) {
            throw new OutOfBoundsException('Search limit must be an integer larger than zero.');
        }

        $this->limit = $limit;

        return $this;
    }
}
