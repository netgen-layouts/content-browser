<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

final class PagerFactory implements PagerFactoryInterface
{
    /**
     * @var int
     */
    private $defaultLimit;

    /**
     * @var int
     */
    private $maxLimit;

    public function __construct(int $defaultLimit, int $maxLimit)
    {
        $this->defaultLimit = $defaultLimit;
        $this->maxLimit = $maxLimit;
    }

    public function buildPager(AdapterInterface $adapter, int $page, ?int $limit = null): Pagerfanta
    {
        $limit = $limit ?? $this->defaultLimit;

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $this->maxLimit ? $limit : $this->maxLimit);
        $pager->setCurrentPage($page > 0 ? $page : 1);

        return $pager;
    }
}
