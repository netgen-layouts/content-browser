<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

final class SearchResult implements SearchResultInterface
{
    /**
     * @var iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    private $results;

    /**
     * @param iterable<\Netgen\ContentBrowser\Item\ItemInterface> $results
     */
    public function __construct(iterable $results = [])
    {
        $this->results = $results;
    }

    public function getResults(): iterable
    {
        return $this->results;
    }
}
