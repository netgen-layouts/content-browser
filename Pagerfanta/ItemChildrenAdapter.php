<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemChildrenAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var int|string
     */
    protected $itemId;

    /**
     * @var int
     */
    protected $nbResults;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     * @param int|string $itemId
     */
    public function __construct(BackendInterface $backend, $itemId)
    {
        $this->backend = $backend;
        $this->itemId = $itemId;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->getChildrenCount($this->itemId);
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
        $children = $this->backend->getChildren(
            $this->itemId,
            array(
                'offset' => $offset,
                'limit' => $length,
            )
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->getChildrenCount($this->itemId);
        }

        return $children;
    }
}
