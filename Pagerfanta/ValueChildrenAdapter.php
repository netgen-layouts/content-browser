<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ValueChildrenAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface
     */
    protected $value;

    /**
     * @var int
     */
    protected $nbResults;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     */
    public function __construct(BackendInterface $backend, ValueInterface $value)
    {
        $this->backend = $backend;
        $this->value = $value;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->getChildrenCount($this->value);
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
            $this->value,
            array(
                'offset' => $offset,
                'limit' => $length,
            )
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->backend->getChildrenCount($this->value);
        }

        return $children;
    }
}
