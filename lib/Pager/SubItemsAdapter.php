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
    private BackendInterface $backend;

    private LocationInterface $location;

    public function __construct(BackendInterface $backend, LocationInterface $location)
    {
        $this->backend = $backend;
        $this->location = $location;
    }

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
    public function getSlice($offset, $length): iterable
    {
        return $this->backend->getSubItems($this->location, $offset, $length);
    }
}
