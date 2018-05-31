<?php

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

    /**
     * @param int $defaultLimit
     * @param int $maxLimit
     */
    public function __construct($defaultLimit, $maxLimit)
    {
        $this->defaultLimit = (int) $defaultLimit;
        $this->maxLimit = (int) $maxLimit;
    }

    public function buildPager(AdapterInterface $adapter, $page, $limit = null)
    {
        $page = (int) $page;
        $limit = $limit !== null ? (int) $limit : $this->defaultLimit;

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $this->maxLimit ? $limit : $this->maxLimit);
        $pager->setCurrentPage($page > 0 ? $page : 1);

        return $pager;
    }
}
