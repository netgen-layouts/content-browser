<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Pager;

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

    public function __construct(BackendInterface $backend, string $searchText)
    {
        $this->backend = $backend;
        $this->searchText = $searchText;
    }

    public function getNbResults(): int
    {
        return $this->backend->searchCount($this->searchText);
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSlice($offset, $length)
    {
        return $this->backend->search($this->searchText, $offset, $length);
    }
}
