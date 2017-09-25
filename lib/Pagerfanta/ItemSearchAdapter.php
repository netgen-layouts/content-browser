<?php

namespace Netgen\ContentBrowser\Pagerfanta;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Pagerfanta\Adapter\AdapterInterface;

final class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var string
     */
    private $searchText;

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

    public function getNbResults()
    {
        return $this->backend->searchCount($this->searchText);
    }

    public function getSlice($offset, $length)
    {
        return $this->backend->search(
            $this->searchText,
            $offset,
            $length
        );
    }
}
