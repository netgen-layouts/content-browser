<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

final class SearchResult implements SearchResultInterface
{
    /**
     * @param iterable<\Netgen\ContentBrowser\Item\ItemInterface> $results
     */
    public function __construct(
        public private(set) iterable $results = [],
    ) {}
}
