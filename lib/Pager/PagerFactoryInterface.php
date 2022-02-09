<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\PagerfantaInterface;

interface PagerFactoryInterface
{
    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface<\Netgen\ContentBrowser\Item\ItemInterface> $adapter
     *
     * @return \Pagerfanta\PagerfantaInterface<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function buildPager(AdapterInterface $adapter, int $page, int $limit): PagerfantaInterface;
}
