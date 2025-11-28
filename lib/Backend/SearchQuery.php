<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Netgen\ContentBrowser\Item\LocationInterface;

final class SearchQuery
{
    /**
     * Returns the offset with which the search is performed.
     */
    public int $offset = 0 {
        /*
         * Sets the offset with which the search is performed.
         *
         * Must be equal or larger than zero.
         */
        set {
            if ($value < 0) {
                throw new OutOfBoundsException('Search offset must be an integer equal or larger than zero.');
            }

            $this->offset = $value;
        }
    }

    /**
     * Returns the limit with which the search is performed.
     */
    public int $limit = 25 {
        /*
         * Sets the limit with which the search is performed.
         *
         * Must be larger than zero.
         */
        set {
            if ($value <= 0) {
                throw new OutOfBoundsException('Search limit must be an integer larger than zero.');
            }

            $this->limit = $value;
        }
    }

    public function __construct(
        /**
         * Returns the search text in the query.
         */
        public private(set) string $searchText,
        /**
         * Returns the location in which to perform the search.
         */
        public private(set) ?LocationInterface $location = null,
    ) {}
}
