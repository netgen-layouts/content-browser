<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Backend\BackendInterface as BaseBackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResultInterface;

interface BackendInterface extends BaseBackendInterface
{
    public function searchItems(SearchQuery $searchQuery): SearchResultInterface;

    public function searchItemsCount(SearchQuery $searchQuery): int;
}
