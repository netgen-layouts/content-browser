<?php

namespace Netgen\Bundle\ContentBrowserBundle\Pagerfanta;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ItemSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var string
     */
    protected $searchText;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param string $searchText
     * @param string $valueType
     */
    public function __construct(ItemRepositoryInterface $itemRepository, $searchText, $valueType)
    {
        $this->itemRepository = $itemRepository;
        $this->searchText = $searchText;
        $this->valueType = $valueType;
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
            $this->valueType
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
            $this->valueType,
            $offset,
            $length
        );
    }
}
