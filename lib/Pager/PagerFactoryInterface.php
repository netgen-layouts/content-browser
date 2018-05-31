<?php

namespace Netgen\ContentBrowser\Pager;

use Pagerfanta\Adapter\AdapterInterface;

interface PagerFactoryInterface
{
    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param int $page
     * @param int $limit
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function buildPager(AdapterInterface $adapter, $page, $limit = null);
}
