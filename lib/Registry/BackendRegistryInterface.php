<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\ContentBrowser\Backend\BackendInterface;

interface BackendRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a backend to registry.
     *
     * @param string $itemType
     * @param \Netgen\ContentBrowser\Backend\BackendInterface $backend
     */
    public function addBackend($itemType, BackendInterface $backend);

    /**
     * Returns if registry has a backend.
     *
     * @param string $itemType
     *
     * @return bool
     */
    public function hasBackend($itemType);

    /**
     * Returns a backend for provided item type.
     *
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If backend does not exist
     *
     * @return \Netgen\ContentBrowser\Backend\BackendInterface
     */
    public function getBackend($itemType);

    /**
     * Returns all backends.
     *
     * @return \Netgen\ContentBrowser\Backend\BackendInterface[]
     */
    public function getBackends();
}
