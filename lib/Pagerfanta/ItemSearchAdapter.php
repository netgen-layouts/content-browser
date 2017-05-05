<?php

namespace Netgen\ContentBrowser\Pagerfanta;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var string
     */
    protected $searchText;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Backend\BackendInterface $backend
     * @param string $searchText
     */
    public function __construct(BackendInterface $backend, $searchText)
    {
        $this->backend = $backend;
        $this->searchText = $searchText;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        return $this->backend->searchCount($this->searchText);
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
        return $this->backend->search(
            $this->searchText,
            $offset,
            $length
        );
    }
}
