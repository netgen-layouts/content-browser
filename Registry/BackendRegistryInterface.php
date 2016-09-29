<?php

namespace Netgen\Bundle\ContentBrowserBundle\Registry;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;

interface BackendRegistryInterface
{
    /**
     * Adds a backend to registry.
     *
     * @param string $itemType
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
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
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If backend does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    public function getBackend($itemType);

    /**
     * Returns all backends.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface[]
     */
    public function getBackends();
}
