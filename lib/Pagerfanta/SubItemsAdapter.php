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
    protected $backend;

    /**
     * @var \Netgen\ContentBrowser\Item\LocationInterface
     */
    protected $location;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Backend\BackendInterface $backend
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     */
    public function __construct(BackendInterface $backend, LocationInterface $location)
    {
        $this->backend = $backend;
        $this->location = $location;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        return $this->backend->getSubItemsCount($this->location);
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return array
     */
    public function getSlice($offset, $length)
    {
        return $this->backend->getSubItems(
            $this->location,
            $offset,
            $length
        );
    }
}
