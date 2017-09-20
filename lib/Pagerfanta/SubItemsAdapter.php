<?php

namespace Netgen\ContentBrowser\Pagerfanta;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Pagerfanta\Adapter\AdapterInterface;

class SubItemsAdapter implements AdapterInterface
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

    public function getNbResults()
    {
        return $this->backend->getSubItemsCount($this->location);
    }

    public function getSlice($offset, $length)
    {
        return $this->backend->getSubItems(
            $this->location,
            $offset,
            $length
        );
    }
}
