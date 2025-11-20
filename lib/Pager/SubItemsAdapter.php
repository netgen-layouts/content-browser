<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Netgen\ContentBrowser\Item\ItemInterface>
 */
final class SubItemsAdapter implements AdapterInterface
{
    public function __construct(
        private BackendInterface $backend,
        private LocationInterface $location,
    ) {}

    public function getNbResults(): int
    {
        return $this->backend->getSubItemsCount($this->location);
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return iterable<int, \Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSlice(int $offset, int $length): iterable
    {
        return $this->backend->getSubItems($this->location, $offset, $length);
    }
}
