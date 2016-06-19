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
     * @var int
     */
    protected $nbResults;

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
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->searchCount(
                $this->searchText,
                $this->valueType
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
        $children = $this->itemRepository->search(
            $this->searchText,
            $this->valueType,
            $offset,
            $length
        );

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->itemRepository->searchCount(
                $this->searchText,
                $this->valueType
            );
        }

        return $children;
    }
}
