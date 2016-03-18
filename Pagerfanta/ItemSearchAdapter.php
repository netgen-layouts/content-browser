<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var string
     */
    protected $searchText;

    /**
     * @var int
     */
    protected $nbResults;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
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
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->searchCount($this->searchText);
        }

        return $this->nbResults;
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     *
     * @return array
     */
    public function getSlice($offset, $length)
    {
        $children = $this->backend->search(
            $this->searchText,
            array(
                'offset' => $offset,
                'limit' => $length
            )
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->searchCount($this->searchText);
        }

        return $children;
    }
}
