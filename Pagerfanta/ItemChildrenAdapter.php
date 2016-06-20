<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemChildrenAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected $item;

    /**
     * @var int
     */
    protected $nbResults;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     */
    public function __construct(ItemRepositoryInterface $itemRepository, ItemInterface $item)
    {
        $this->itemRepository = $itemRepository;
        $this->item = $item;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->getSubItemsCount($this->item);
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
            $this->item,
            $offset,
            $length
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->getSubItemsCount($this->item);
        }

        return $children;
    }
}
