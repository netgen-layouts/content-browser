<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

final class PagerFactory implements PagerFactoryInterface
{
    private int $maxLimit;

    public function __construct(int $maxLimit)
    {
        $this->maxLimit = $maxLimit;
    }

    public function buildPager(AdapterInterface $adapter, int $page, int $limit): PagerfantaInterface
    {
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $this->maxLimit ? $limit : $this->maxLimit);
        $pager->setCurrentPage($page > 0 ? $page : 1);

        return $pager;
    }
}
