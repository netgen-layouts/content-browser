<?php

namespace Netgen\ContentBrowser\Pagerfanta;

use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Pagerfanta\Adapter\AdapterInterface;

class SubItemsAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\ContentBrowser\Item\LocationInterface
     */
    protected $location;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
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
        return $this->itemRepository->getSubItemsCount(
            $this->location
        );
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
        return $this->itemRepository->getSubItems(
            $this->location,
            $offset,
            $length
        );
    }
}
