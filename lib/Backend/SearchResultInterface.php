<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

interface SearchResultInterface
{
    /**
     * Returns the result of running the search query.
     *
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getResults(): iterable;
}
