<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Pagerfanta\Adapter\AdapterInterface;

final class SubItemsAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Item\LocationInterface
     */
    private $location;

    public function __construct(BackendInterface $backend, LocationInterface $location)
    {
        $this->backend = $backend;
        $this->location = $location;
    }

    public function getNbResults(): int
    {
        return $this->backend->getSubItemsCount($this->location);
    }

    public function getSlice($offset, $length)
    {
        return $this->backend->getSubItems($this->location, $offset, $length);
    }
}
