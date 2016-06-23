<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Pagerfanta\Adapter\AdapterInterface;

class SubItemsAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface
     */
    protected $location;

    /**
     * @var int
     */
    protected $nbResults;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     */
    public function __construct(ItemRepositoryInterface $itemRepository, LocationInterface $location)
    {
        $this->itemRepository = $itemRepository;
        $this->location = $location;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->getSubItemsCount(
                $this->location
            );
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
        $children = $this->itemRepository->getSubItems(
            $this->location,
            $offset,
            $length
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->getSubItemsCount(
                $this->location
            );
        }

        return $children;
    }
}
