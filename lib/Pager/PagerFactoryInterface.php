<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

interface PagerFactoryInterface
{
    /**
     * Builds the pager from provided adapter.
     */
    public function buildPager(AdapterInterface $adapter, int $page, int $limit): Pagerfanta;
}
