<?php

namespace Netgen\ContentBrowser\Pagerfanta;

use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var string
     */
    protected $searchText;

    /**
     * @var string
     */
    protected $itemType;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Item\ItemRepositoryInterface $itemRepository
     * @param string $searchText
     * @param string $itemType
     */
    public function __construct(ItemRepositoryInterface $itemRepository, $searchText, $itemType)
    {
        $this->itemRepository = $itemRepository;
        $this->searchText = $searchText;
        $this->itemType = $itemType;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults()
    {
        return $this->itemRepository->searchCount(
            $this->searchText,
            $this->itemType
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
        return $this->itemRepository->search(
            $this->searchText,
            $this->itemType,
            $offset,
            $length
        );
    }
}
